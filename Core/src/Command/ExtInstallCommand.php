<?php
declare(strict_types=1);

namespace Croogo\Core\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Console\Exception\ConsoleException;
use Cake\Utility\Inflector;
use Croogo\Core\PluginManager;
use Croogo\Extensions\CroogoTheme;
use Croogo\Extensions\ExtensionsInstaller;

/**
 * Port Core InstallShell (Cake 5: Shell -> Command).
 *
 * Pobiera, instaluje i aktywuje rozszerzenia (pluginy/motywy):
 *  Composer: bin/cake ext install plugin vendor/package
 *  Github:   bin/cake ext install plugin user repo
 *  Url:      bin/cake ext install plugin https://github.com/user/repo
 */
class ExtInstallCommand extends Command
{
    /**
     * Tmp path to download extensions to
     *
     * @var string
     */
    public $tmpPath = TMP;

    /**
     * @var \Croogo\Extensions\ExtensionsInstaller
     */
    protected $_ExtensionsInstaller;

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
        return 'ext install';
    }

    public static function getDescription(): string
    {
        return __d('croogo', 'Download, Install & Activate Plugins & Themes');
    }

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription(static::getDescription())
            ->addArguments([
                'type' => [
                    'help' => __d('croogo', 'Extension type'),
                    'required' => true,
                    'choices' => ['plugin', 'theme'],
                ],
                'zip_url' => [
                    'help' => __d('croogo', 'URL to zip file OR github user name OR composer package'),
                    'required' => true,
                ],
                'github_package' => [
                    'help' => __d('croogo', 'Github repo name OR composer version'),
                ],
            ]);
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $this->_ExtensionsInstaller = new ExtensionsInstaller();
        $this->_CroogoPlugin = new PluginManager();
        $this->_CroogoTheme = new CroogoTheme();

        $type = (string)$args->getArgument('type');
        $package = (string)$args->getArgument('zip_url');
        $extra = $args->getArgument('github_package');

        if (strpos($package, '/') !== false && preg_match('/http[s]*:\/\//i', $package) === 0) {
            // Composer Install
            $ver = $extra !== null ? $extra : '*';
            $io->out(__d('croogo', 'Installing with Composer...'));
            try {
                $result = $this->_ExtensionsInstaller->composerInstall([
                    'package' => $package,
                    'version' => $ver,
                    'type' => $type,
                ]);
                if (!is_array($result)) {
                    $io->err(__d('croogo', 'Unexpected composerInstall return value'));

                    return static::CODE_ERROR;
                }
                if ($result['returnValue'] <> 0) {
                    $io->err($result['output']);

                    return static::CODE_ERROR;
                }
                $ext = substr($package, strpos($package, '/') + 1);
                $ext = Inflector::camelize($ext);
                $shouldActivate = $this->{'_Croogo' . ucfirst($type)}->getData($ext);
                if ($shouldActivate !== false) {
                    $result = $this->executeCommand(ExtCommand::class, ['activate', $type, $ext, '--quiet'], $io);
                    if ($result === static::CODE_SUCCESS || $result === null) {
                        $io->out(__d('croogo', 'Package installed and activated.'));
                    } else {
                        $io->err(__d('croogo', 'Package installed but not activated.'));
                    }
                }
            } catch (\Exception $e) {
                $io->err($e->getMessage());
            }
        } else {
            // Github / URL Install
            $url = '';
            if ($extra === null) {
                $url = $package;
            } else {
                $url = 'http://github.com/' . $package . '/' . $extra;
            }
            if ($zip = $this->_download($io, $url)) {
                if ($this->_install($io, $type, $zip)) {
                    if ($this->_activate($io, $type, $zip)) {
                        $io->out(__d('croogo', 'Extension installed and activated.'));
                    }
                }
            }
        }

        return static::CODE_SUCCESS;
    }

    /**
     * Activates an extension by calling ExtCommand
     */
    protected function _activate(ConsoleIo $io, $type = null, $zip = null): bool
    {
        try {
            $ext = $this->_ExtensionsInstaller->{'get' . ucfirst($type) . 'Name'}($zip);
            $this->executeCommand(ExtCommand::class, ['activate', $type, $ext, '--quiet'], $io);

            return true;
        } catch (\Exception $e) {
            $io->err($e->getMessage());
        }

        return false;
    }

    /**
     * Extracts an extension
     */
    protected function _install(ConsoleIo $io, $type = null, $zip = null): bool
    {
        $io->out(__d('croogo', 'Installing extension...'));
        try {
            $this->_ExtensionsInstaller->{'extract' . ucfirst($type)}($zip);

            return true;
        } catch (\Exception $e) {
            $io->err($e->getMessage());
        }

        return false;
    }

    /**
     * Download an extension via CURL
     *
     * @return string|false Path to zip file
     * @throws \Cake\Console\Exception\ConsoleException
     */
    protected function _download(ConsoleIo $io, $url = null)
    {
        if (empty($url)) {
            throw new ConsoleException(__d('croogo', 'Please specify a URL to a zipball extension'));
        }
        $url = $this->_githubUrl($url);
        $filename = uniqid('croogo_') . '.zip';
        $zip = $this->tmpPath . $filename;
        $io->out(__d('croogo', 'Downloading extension to %s...', $zip));
        $res = $this->_shellExec('curl -L ' . $url . ' -o ' . $zip . ' 2>&1');

        return $res ? $zip : false;
    }

    /**
     * If Github url return url to zip
     */
    protected function _githubUrl($url = null): string
    {
        if (strpos($url, 'github.com') === false) {
            return $url;
        }
        if (substr($url, -1) === '/') {
            $url = substr($url, 0, -1);
        }
        if (substr($url, -4) === '.git') {
            $url = substr($url, 0, -4);
        }
        $url = str_replace('git://', 'https://', $url);

        return $url . '/zipball/master';
    }

    /**
     * Wrapper for shell_exec() method for testing
     */
    protected function _shellExec($cmd)
    {
        return shell_exec($cmd);
    }
}
