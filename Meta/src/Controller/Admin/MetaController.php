<?php

namespace Croogo\Meta\Controller\Admin;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Croogo\Meta\Controller\AppController;

/**
 * Meta Controller
 *
 * @category Meta.Controller
 * @package  Croogo.Meta
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MetaController extends AppController
{

/**
 * Preset Variable Search
 *
 * @var array
 * @access public
 */
    public $presetVars = true;

    public function initialize()
    {
        parent::initialize();

        $this->components()->unload('Meta');
        unset($this->Meta);
        $this->loadModel('Croogo/Meta.Meta');

        $this->Crud->config('actions.index', [
            'displayFields' => $this->Meta->displayFields(),
            'searchFields' => ['key', 'value'],
            'relatedModels' => false
        ]);
        $this->Crud->config('actions.edit', [
            'editFields' => $this->Meta->editFields(),
            'relatedModels' => false
        ]);
        $this->Crud->config('actions.add', [
            'editFields' => $this->Meta->editFields(),
            'relatedModels' => false
        ]);

        $this->_setupPrg();
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->Crud->on('Crud.beforePaginate', function (Event $event) {
           $event->subject()->query->where(['model' => '']);
        });
        $this->Crud->on('Crud.beforeSave', function (Event $event) {
            $entity = $event->subject()->entity;
            if (empty($entity->model)) {
                $entity->model = '';
            }
        });
    }

    /**
 * Admin delete meta
 *
 * @param int $id
 * @return void
 * @access public
 */
    public function deleteMeta($id = null)
    {
        $Meta = TableRegistry::get('Croogo/Meta.Meta');
        $success = false;
        $meta = $Meta->findById($id)->first();
        if ($meta !== null && $Meta->delete($meta)) {
            $success = true;
        } elseif ($meta === null) {
            $success = true;
        }

        $success = ['success' => $success];
        $this->set(compact('success'));
        $this->set('_serialize', 'success');
    }

/**
 * Admin add meta
 *
 * @return void
 * @access public
 */
    public function addMeta()
    {
        $this->viewBuilder()->setLayout('ajax');
    }
}
