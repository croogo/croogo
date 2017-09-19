<?php

namespace Croogo\Core\View;

use App\View\AppView;
use Cake\Core\App;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Croogo\Core\Croogo;
use Croogo\Extensions\CroogoTheme;

/**
 * Class CroogoView
 *
 * @property \Croogo\Core\View\Helper\CroogoHelper $Croogo
 * @property \Croogo\Menus\View\Helper\MenusHelper $Menus
 */
class CroogoView extends AppView
{

    /**
     * Return all possible paths to find view files in order
     *
     * @param string|null $plugin Optional plugin name to scan for view files.
     * @param bool $cached Set to false to force a refresh of view paths. Default true.
     * @return array paths
     */
    protected function _paths($plugin = null, $cached = true)
    {
        if ($cached === true) {
            if ($plugin === null && !empty($this->_paths)) {
                return $this->_paths;
            }
            if ($plugin !== null && isset($this->_pathsForPlugin[$plugin])) {
                return $this->_pathsForPlugin[$plugin];
            }
        }
        $templatePaths = App::path(static::NAME_TEMPLATE);
        $pluginPaths = $themePaths = [];
        if (!empty($plugin)) {
            for ($i = 0, $count = count($templatePaths); $i < $count; $i++) {
                $pluginPaths[] = $templatePaths[$i] . 'Plugin' . DIRECTORY_SEPARATOR . $plugin . DIRECTORY_SEPARATOR;
            }
            $pluginPaths = array_merge($pluginPaths, App::path(static::NAME_TEMPLATE, $plugin));
        }

        if (!empty($this->theme)) {
            $themePaths = App::path(static::NAME_TEMPLATE, Inflector::camelize($this->theme));
            array_unshift($themePaths, APP . 'Template' . DIRECTORY_SEPARATOR . 'Plugin' . DIRECTORY_SEPARATOR . $this->theme . DIRECTORY_SEPARATOR);

            if ($plugin) {
                foreach (array_reverse($themePaths) as $path) {
                    array_unshift($themePaths, $path . 'Plugin' . DIRECTORY_SEPARATOR . $plugin . DIRECTORY_SEPARATOR);
                }
            }
        }

        $paths = array_merge(
            $themePaths,
            $pluginPaths,
            $templatePaths,
            [dirname(__DIR__) . DIRECTORY_SEPARATOR . static::NAME_TEMPLATE . DIRECTORY_SEPARATOR]
        );

        if ($plugin !== null) {
            return $this->_pathsForPlugin[$plugin] = $paths;
        }

        return $this->_paths = $paths;
    }

    public function loadHelpers()
    {
        parent::loadHelpers();

        $prefix = $this->request->param('prefix') ?: '';
        if ($prefix === 'admin') {
            $this->loadHelper('Croogo/Core.Croogo');
        }

        $themeConfig = CroogoTheme::config($this->theme());
        if (!empty($themeConfig['settings']['prefixes'][$prefix]['helpers'])) {
            $this->loadHelperList($themeConfig['settings']['prefixes'][$prefix]['helpers']);
        }

        $hookHelpers = Croogo::options('Hook.view_builder_options', $this->request, 'helpers');

        $this->loadHelperList($hookHelpers);
        $this->loadHelper('Time', [
            'outputTimezone' => $this->request->session()->read('Auth.User.timezone'),
        ]);
    }

    public function loadHelperList($list)
    {
        foreach ($list as $helper => $config) {
            if (!is_array($config)) {
                $helper = $config;
                $config = [];
            }
            if ($this->helpers()->has($helper)) {
                continue;
            }
            $this->loadHelper($helper, $config);
        }
    }
}
