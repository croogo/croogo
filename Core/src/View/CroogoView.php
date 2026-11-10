<?php

namespace Croogo\Core\View;

use App\View\AppView;
use Cake\Core\App;
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
    protected function _paths(?string $plugin = null, bool $cached = true): array
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
            // Cake 5: szablony Core leżą w Core/templates/, nie Core/src/templates/.
            // Z Core/src/View trzeba wyjść 2 poziomy (dirname(__DIR__,2) = Core).
            [dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . static::NAME_TEMPLATE . DIRECTORY_SEPARATOR]
        );

        if ($plugin !== null) {
            return $this->_pathsForPlugin[$plugin] = $paths;
        }

        return $this->_paths = $paths;
    }

    /**
     * Cake 5: helpery są dostępne wyłącznie przez View::__get() (bez właściwości i bez
     * __isset), więc isset($this->Paginator) zawsze zwracało false — a ~34 szablony admina
     * bramkują na tym blok paginacji. Odtwarzamy semantykę „helper istnieje".
     *
     * @param string $name Helper name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        if ($this->helpers()->has($name)) {
            return true;
        }
        try {
            return $this->loadHelper($name) !== null;
        } catch (\Throwable) {
            return false;
        }
    }

    public function loadHelpers(): void
    {
        // Aliasuj Html/Form na wersje Croogo PRZED parent (inaczej parent ładuje
        // bazowe Cake helpery i theme nie może ich nadpisać -> brak m.in. Html::icon()).
        if (!$this->helpers()->has('Html')) {
            $this->loadHelper('Html', ['className' => 'Croogo/Core.Html']);
        }
        if (!$this->helpers()->has('Form')) {
            $this->loadHelper('Form', ['className' => 'Croogo/Core.Form']);
        }

        parent::loadHelpers();

        // Cake 5: helpery nie doładowują się magicznie na poziomie widoku, a szablony
        // Croogo używają $this->Theme wprost - gwarantujemy jego obecność.
        if (!$this->helpers()->has('Theme')) {
            $this->loadHelper('Croogo/Core.Theme');
        }

        $prefix = $this->getRequest()->getParam('prefix') ?: '';
        if ($prefix === 'Admin') {
            $this->loadHelper('Croogo/Core.Croogo');
        }

        $themeConfig = CroogoTheme::config($this->getTheme());
        if (!empty($themeConfig['settings']['prefixes'][$prefix]['helpers'])) {
            $this->loadHelperList($themeConfig['settings']['prefixes'][$prefix]['helpers']);
        }

        $hookHelpers = Croogo::options('Hook.view_builder_options', $this->getRequest(), 'helpers');

        $this->loadHelperList($hookHelpers);
        $this->loadHelper('Time', [
            'outputTimezone' => $this->getRequest()->getSession()->read('Auth.User.timezone'),
        ]);
    }

    public function loadHelperList($list): void
    {
        foreach ((array)$list as $helper => $config) {
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
