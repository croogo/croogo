<?php

namespace Croogo\Meta\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Croogo\Core\Croogo;

/**
 * Meta Component
 *
 * @package Croogo.Meta.Controller.Component
 */
class MetaComponent extends Component
{
    /**
     * startup
     */
    public function startup()
    {
        $controller = $this->_registry->getController();
        if ($controller->request->getParam('prefix') === 'admin') {
            $this->_adminTabs();

            if (empty($controller->request->getData('meta'))) {
                return;
            }
            $unlockedFields = [];
            foreach ($controller->request->getData('meta') as $uuid => $fields) {
                foreach ($fields as $field => $vals) {
                    $unlockedFields[] = 'meta.' . $uuid . '.' . $field;
                }
            }
            $controller->Security->setConfig('unlockedFields', $unlockedFields);
        }
    }

    /**
     * Hook admin tabs for controllers whom its primary model has MetaBehavior attached.
     */
    protected function _adminTabs()
    {
        $controller = $this->_registry->getController();
        $table = TableRegistry::get($controller->modelClass);
        if ($table &&
            !$table->behaviors()
                ->has('Meta')
        ) {
            return;
        }
        $title = __d('croogo', 'Custom Fields');
        $element = 'Croogo/Meta.admin/meta_tab';
        $controllerName = $this->request->getParam('controller');
        Croogo::hookAdminBox("Admin/$controllerName/add", $title, $element);
        Croogo::hookAdminBox("Admin/$controllerName/edit", $title, $element);
    }
}
