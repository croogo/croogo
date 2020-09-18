<?php
declare(strict_types=1);

namespace Croogo\Meta\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
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
        $request = $controller->getRequest();
        if ($request->getParam('prefix') === 'Admin') {
            $this->_adminTabs();

            if (empty($request->getData('meta'))) {
                return;
            }
            $unlockedFields = [];
            foreach ($request->getData('meta') as $uuid => $fields) {
                foreach ($fields as $field => $vals) {
                    $unlockedFields[] = 'meta.' . $uuid . '.' . $field;
                }
            }
            $controller->Security->setConfig('unlockedFields', $unlockedFields);
        } else {
            $this->loadMeta();
        }
    }

    protected function loadMeta()
    {
        $Meta = TableRegistry::get('Croogo/Meta.Meta');
        $defaultMeta = $Meta->find()
            ->select(['key', 'value'])
            ->where([
                'foreign_key IS' => null,
            ]);
        $data = [];
        foreach ($defaultMeta as $meta) {
            $data[$meta->key] = $meta->value;
        }
        Configure::write('Meta.data', $data);
    }

    /**
     * Hook admin tabs for controllers whom its primary model has MetaBehavior attached.
     */
    protected function _adminTabs()
    {
        $controller = $this->getController();
        $table = TableRegistry::get($controller->getName());
        if ($table &&
            !$table->behaviors()
                ->has('Meta')
        ) {
            return;
        }
        $title = __d('croogo', 'Custom Fields');
        $element = 'Croogo/Meta.admin/custom_fields_box';
        $controllerName = $controller->getRequest()->getParam('controller');
        Croogo::hookAdminBox("Admin/$controllerName/add", $title, $element);
        Croogo::hookAdminBox("Admin/$controllerName/edit", $title, $element);
    }
}
