<?php

namespace Croogo\Core\View;

use App\View\AppView;
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

    public function initialize()
    {
        parent::initialize();
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
