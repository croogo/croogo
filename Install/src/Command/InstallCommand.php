<?php
declare(strict_types=1);

namespace Croogo\Install\Command;

use App\Console\Installer;
use Cake\Cache\Cache;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Composer\IO\BufferIO;
use Croogo\Acl\AclGenerator;
use Croogo\Core\PluginManager;
use Croogo\Install\InstallManager;
use Exception;

/**
 * Port Install/InstallShell::main (Cake 5: Shell -> Command).
 *
 * Instalacja Croogo z CLI: konfiguracja bazy, migracje+seedy, ACL, admin.
 * Usage: bin/cake install [-d Mysql -h host -u user -p pass -n dbname -t port] [admin-user] [admin-password]
 */
class InstallCommand extends Command
{
    public static function defaultName(): string
    {
        return 'install';
    }

    public static function getDescription(): string
    {
        return __d('croogo', 'Install Utilities');
    }

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $drivers = ['Mysql', 'Postgres', 'Sqlite', 'Sqlserver'];

        return $parser
            ->setDescription(__d('croogo', 'Generate database config and create admin user.'))
            ->addOption('datasource', [
                'help' => 'Database Driver',
                'short' => 'd',
                'choices' => $drivers,
            ])
            ->addOption('host', [
                'help' => 'Database Host',
            ])
            ->addOption('username', [
                'help' => 'Database User',
                'short' => 'u',
            ])
            ->addOption('password', [
                'help' => 'Database Password',
                'short' => 'p',
            ])
            ->addOption('database-name', [
                'help' => 'Database Name',
                'short' => 'n',
            ])
            ->addOption('port', [
                'help' => 'Database Port',
                'short' => 't',
            ])
            ->addArgument('admin-user', [
                'help' => 'Admin username',
            ])
            ->addArgument('admin-password', [
                'help' => 'Admin password',
            ]);
    }

    /**
     * Ładuje pluginy Croogo (odpowiednik Shell::startup)
     */
    protected function _loadPlugins(): void
    {
        $options = ['bootstrap' => true, 'routes' => true];
        $plugins = array_merge(PluginManager::$corePlugins, PluginManager::$bundledPlugins);
        foreach ($plugins as $plugin) {
            if (!Plugin::isLoaded($plugin)) {
                PluginManager::load($plugin, $options);
            }
        }
    }

    /**
     * Pobiera wartość z opcji CLI albo pyta interaktywnie
     */
    protected function _in(Arguments $args, ConsoleIo $io, string $prompt, $options = null, $default = null, ?string $option = null)
    {
        if ($option !== null && $args->getOption($option) !== null) {
            return $args->getOption($option);
        }
        if (is_array($options)) {
            return $io->askChoice($prompt, $options, $default);
        }

        return $io->ask($prompt, (string)$default);
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $this->_loadPlugins();

        Installer::setSecuritySalt(ROOT, new BufferIO());
        $io->out();
        $io->out('Database settings:');
        $install = [];
        $install['datasource'] = $this->_in($args, $io, __d('croogo', 'DataSource'), [
            'Mysql',
            'Sqlite',
            'Postgres',
            'Sqlserver',
        ], 'Mysql', 'datasource');
        $install['driver'] = 'Cake\Database\Driver\\' . $install['datasource'];
        $install['host'] = $this->_in($args, $io, __d('croogo', 'Host'), null, 'localhost', 'host');
        $install['username'] = $this->_in($args, $io, __d('croogo', 'Login'), null, 'root', 'username');
        $install['password'] = $this->_in($args, $io, __d('croogo', 'Password'), null, '', 'password');
        $install['database'] = $this->_in($args, $io, __d('croogo', 'Database'), null, 'croogo', 'database-name');
        $install['port'] = $this->_in($args, $io, __d('croogo', 'Port'), null, '', 'port');

        $InstallManager = new InstallManager();
        $isFileCreated = $InstallManager->createDatabaseFile($install);
        if ($isFileCreated !== true) {
            $io->err($isFileCreated);

            return static::CODE_ERROR;
        }

        $io->out('Setting up database objects. Please wait...');
        try {
            $result = $InstallManager->setupDatabase();
            if ($result !== true) {
                $io->err((string)$result);

                return static::CODE_ERROR;
            }
        } catch (Exception $e) {
            $io->err($e->getMessage());
            $io->err('Please verify you have the correct credentials');

            return static::CODE_ERROR;
        }

        try {
            $io->out('Setting up access control objects. Please wait...');
            $generator = new AclGenerator();
            $generator->setShell($io);
            $generator->insertAcos(ConnectionManager::get('default'));
            $InstallManager->setupGrants();
        } catch (Exception $e) {
            $io->err('Error installing access control objects');
            $io->err($e->getMessage());

            return static::CODE_ERROR;
        }

        $username = $args->getArgument('admin-user');
        $password = $args->getArgument('admin-password');

        if (!$username || !$password) {
            $io->out();
            $io->out('Create Admin user:');
        }

        while (empty($username)) {
            $username = $io->ask(__d('croogo', 'Username'));
            if (empty($username)) {
                $io->err('Username must not be empty');
            }
        }

        $passwordsMatched = $password !== null;
        while (empty($password) || !$passwordsMatched) {
            $password = $io->ask(__d('croogo', 'Password'));
            $verify = $io->ask(__d('croogo', 'Verify Password'));
            $passwordsMatched = $password == $verify;
            if (!$passwordsMatched) {
                $io->err('Passwords do not match');
            }
            if (empty($password)) {
                $io->err('Password must not be empty');
            }
        }

        $user = ['username' => $username, 'password' => $password];

        try {
            $io->out('Setting up admin user. Please wait...');
            $Install = $this->fetchTable('Croogo/Install.Install');
            $Install->addAdminUser($user);
            $InstallManager->installCompleted();
        } catch (Exception $e) {
            $io->err('Error creating admin user: ' . $e->getMessage());
        }

        $io->out();
        $io->success('Congratulations, Croogo has been installed successfully.');
        Cache::clearAll();

        return static::CODE_SUCCESS;
    }
}
