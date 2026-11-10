<?php
declare(strict_types=1);

namespace Croogo\Core\Command;

use App\Controller\AppController;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Http\ServerRequest;
use Croogo\Core\PluginManager as CroogoPlugin;
use Croogo\Extensions\CroogoTheme;

/**
 * Port ExtShell (Cake 5: Shell -> Command).
 *
 * Activate/Deactivate Plugins/Themes
 *  bin/cake ext activate plugin Example
 *  bin/cake ext activate theme minimal
 *  bin/cake ext deactivate plugin Example
 *  bin/cake ext list plugin -a
 */
class ExtCommand extends Command
{
    /**
     * @var \Croogo\Core\PluginManager
     */
    protected $_CroogoPlugin;

    /**
     * @var \Croogo\Extensions\CroogoTheme
     */
    protected $_CroogoTheme;

    public static function defaultName(): string
    {
        return 'ext';
    }

    public static function getDescription(): string
    {
        return __d('croogo', 'Activate Plugins & Themes');
    }

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription(static::getDescription())
            ->addArguments([
                'method' => [
                    'help' => __d('croogo', 'Method to perform'),
                    'required' => true,
                    'choices' => ['list', 'activate', 'deactivate'],
                ],
                'type' => [
                    'help' => __d('croogo', 'Extension type'),
                    'required' => true,
                    'choices' => ['plugin', 'theme'],
                ],
                'extension' => [
                    'help' => __d('croogo', 'Name of extension'),
                ],
            ])
            ->addOption('all', [
                'short' => 'a',
                'boolean' => true,
                'help' => 'List all extensions',
            ])
            ->addOption('force', [
                'short' => 'f',
                'boolean' => true,
                'help' => 'Force method operation even when plugin does not provide a `plugin.json` file.',
            ]);
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $this->_CroogoPlugin = new CroogoPlugin();
        $this->_CroogoTheme = new CroogoTheme();

        // Legacy: PluginActivation hooki dostają kontroler (@todo w oryginale).
        // W CLI startupProcess może nie mieć pełnego kontekstu HTTP - nie blokujemy.
        try {
            $controller = new AppController(new ServerRequest());
            $controller->startupProcess();
            $this->_CroogoPlugin->setController($controller);
        } catch (\Throwable $e) {
            $io->verbose(__d('croogo', 'Controller bootstrap skipped: %s', $e->getMessage()));
        }

        $method = strtolower((string)$args->getArgument('method'));
        $type = strtolower((string)$args->getArgument('type'));
        $ext = $args->getArgument('extension');
        $force = (bool)$args->getOption('force');

        $extensions = [];
        $active = false;
        if ($type == 'theme') {
            $extensions = $this->_CroogoTheme->getThemes();
            $theme = Configure::read('Site.theme');
            $active = !empty($theme) ? $theme == 'default' : true;
        } elseif ($type == 'plugin') {
            $extensions = $this->_CroogoPlugin->getPlugins();
            if ($force) {
                $plugins = array_combine($p = Plugin::loaded(), $p);
                $extensions += $plugins;
            }
            $active = $ext !== null && Plugin::isLoaded($ext);
        }
        if ($type == 'theme' && $method == 'deactivate') {
            $io->err(__d('croogo', 'Theme cannot be deactivated, instead activate another theme.'));

            return static::CODE_ERROR;
        }
        if (!empty($ext) && !isset($extensions[$ext]) && !$active && !$force) {
            $io->err(__d('croogo', '%s "%s" not found.', ucfirst($type), $ext));

            return static::CODE_ERROR;
        }

        switch ($method) {
            case 'list':
                if ($type == 'theme') {
                    $this->_themes($io, $args, $ext);
                } else {
                    $this->_plugins($io, $args, $ext);
                }

                return static::CODE_SUCCESS;
            default:
                if (empty($ext)) {
                    $io->err(__d('croogo', '%s name must be provided.', ucfirst($type)));

                    return static::CODE_ERROR;
                }

                $result = $this->{'_' . $method . ucfirst($type)}($io, $args, $ext);

                return $result ? static::CODE_SUCCESS : static::CODE_ERROR;
        }
    }

    /**
     * Activate a plugin
     */
    protected function _activatePlugin(ConsoleIo $io, Arguments $args, string $plugin): bool
    {
        $result = $this->_CroogoPlugin->activate($plugin);
        if ($result === true) {
            $io->out(__d('croogo', 'Plugin "%s" activated successfully.', $plugin));

            return true;
        } elseif (is_string($result)) {
            $io->err($result);
        } else {
            $io->err(__d('croogo', 'Plugin "%s" could not be activated. Please, try again.', $plugin));
        }

        return false;
    }

    /**
     * Deactivate a plugin
     */
    protected function _deactivatePlugin(ConsoleIo $io, Arguments $args, string $plugin): bool
    {
        $force = (bool)$args->getOption('force');
        $result = false;
        $usedBy = $this->_CroogoPlugin->usedBy($plugin);
        if ($usedBy === false) {
            $result = $this->_CroogoPlugin->deactivate($plugin);
            if ($result === false) {
                $io->err(__d('croogo', 'Plugin "%s" could not be deactivated. Please, try again.', $plugin));
            } elseif (is_string($result)) {
                $io->err($result);
            }
        } else {
            if ($force === false) {
                $io->err(__d('croogo', 'Plugin "%s" could not be deactivated since "%s" depends on it.', $plugin, implode(', ', $usedBy)));
            } else {
                $result = true;
            }
        }
        if ($force === true || $result === true) {
            $this->_CroogoPlugin->removeBootstrap($plugin);
            $result = true;
        }
        if ($result === true) {
            $io->out(__d('croogo', 'Plugin "%s" deactivated successfully.', $plugin));

            return true;
        }

        return false;
    }

    /**
     * Activate a theme
     */
    protected function _activateTheme(ConsoleIo $io, Arguments $args, string $theme): bool
    {
        if ($this->_CroogoTheme->activate($theme)) {
            $io->out(__d('croogo', 'Theme "%s" activated successfully.', $theme));
        } else {
            $io->err(__d('croogo', 'Theme "%s" activation failed.', $theme));
        }

        return true;
    }

    /**
     * List plugins
     */
    protected function _plugins(ConsoleIo $io, Arguments $args, $plugin = null): void
    {
        $all = (bool)$args->getOption('all');
        $plugins = $plugin == null ? array_keys((array)Configure::read('plugins')) : [$plugin];
        $loaded = Plugin::loaded();
        $CroogoPlugin = new CroogoPlugin();
        $io->out(__d('croogo', 'Plugins:'), 2);
        $io->out(__d('croogo', '%-20s%-50s%s', __d('croogo', 'Plugin'), __d('croogo', 'Author'), __d('croogo', 'Status')));
        $io->out(str_repeat('-', 80));
        foreach ($plugins as $plugin) {
            $status = '<info>inactive</info>';
            if ($active = in_array($plugin, $loaded)) {
                $status = '<success>active</success>';
            }
            if (!$active && !$all) {
                continue;
            }
            $data = $CroogoPlugin->getData($plugin);
            $author = isset($data['author']) ? $data['author'] : '';
            $io->out(__d('croogo', '%-20s%-50s%s', $plugin, $author, $status));
        }
    }

    /**
     * List themes
     */
    protected function _themes(ConsoleIo $io, Arguments $args, $theme = null): void
    {
        $CroogoTheme = new CroogoTheme();
        $all = (bool)$args->getOption('all');
        $current = Configure::read('Site.theme');
        $themes = $theme == null ? $CroogoTheme->getThemes() : [$theme];
        $io->out("Themes:", 2);
        $default = empty($current) || $current == 'default';
        $io->out(__d('croogo', '%-20s%-50s%s', __d('croogo', 'Theme'), __d('croogo', 'Author'), __d('croogo', 'Status')));
        $io->out(str_repeat('-', 80));
        foreach ($themes as $theme) {
            $active = $theme == $current || $default && $theme == 'default';
            $status = $active ? '<success>active</success>' : '<info>inactive</info>';
            if (!$active && !$all) {
                continue;
            }
            $data = $CroogoTheme->getThemeData($theme);
            $author = isset($data['author']) ? $data['author'] : '';
            $io->out(__d('croogo', '%-20s%-50s%s', $theme, $author, $status));
        }
    }
}
