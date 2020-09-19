<?php
declare(strict_types=1);

namespace Croogo\Core\View;

use App\View\AppView;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
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

    public function loadHelpers()
    {
        parent::loadHelpers();

        $prefix = $this->getRequest()->getParam('prefix') ?: '';
        if ($prefix === 'Admin') {
            $this->loadHelper('Croogo/Core.Croogo');
        }

        $themeConfig = CroogoTheme::config($this->getTheme());
        if (!empty($themeConfig['settings']['prefixes'][$prefix]['helpers'])) {
            $this->loadHelperList($themeConfig['settings']['prefixes'][$prefix]['helpers']);
        }

        $hookHelpers = Croogo::options('Hook.view_builder_options', $this->request, 'helpers');

        $this->loadHelperList($hookHelpers);
        $this->loadHelper('Time', [
            'outputTimezone' => $this->getRequest()->getSession()->read('Auth.User.timezone'),
        ]);
    }

    public function loadHelperList($list)
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

    public function render(?string $template = null, $layout = null): string
    {
        if (in_array($this->getRequest()->getParam('action'), ['edit', 'add'])) {
            try {
                // First try the edit or add template
                return parent::render($template, $layout);
            } catch (MissingTemplateException $e) {
                // Secondly, when isn't found, try form template
                return parent::render('form', $layout);
            }
        }

        try {
            return parent::render($template, $layout);
        } catch (MissingTemplateException $e) {
            if ($this->getRequest()->getParam('_ext')) {
                throw new NotFoundException(null, null);
            }
        }
    }

}
