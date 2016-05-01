<?php

namespace Croogo\Nodes\Controller\Admin;

use Cake\Event\Event;

use Croogo\Core\Controller\Component\CroogoComponent;
use Croogo\Core\Croogo;
use Croogo\Nodes\Model\Entity\Node;
use Croogo\Nodes\Model\Table\NodesTable;
use Croogo\Taxonomy\Controller\Component\TaxonomiesComponent;
use Croogo\Taxonomy\Model\Entity\Type;

/**
 * Nodes Controller
 *
 * @property NodesTable Nodes
 * @property CroogoComponent Croogo
 * @property TaxonomiesComponent Taxonomies
 * @category Nodes.Controller
 * @package  Croogo.Nodes
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class NodesController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Croogo/Core.BulkProcess');
        $this->loadComponent('Croogo/Core.Recaptcha');

        $this->_setupPrg();
    }

    /**
     * Admin create
     *
     * @return void
     * @access public
     */
    public function create()
    {
        $types = $this->Nodes->Taxonomies->Vocabularies->Types->find('all', array(
            'order' => array(
                'Types.alias' => 'ASC',
            ),
        ));

        if ($types->count() === 1) {
            return $this->redirect(['action' => 'add', $types->first()->alias]);
        }

        $this->set(compact('types'));
    }

    /**
     * Admin update paths
     *
     * @return void
     * @access public
     */
    public function update_paths()
    {
        $Node = $this->{$this->modelClass};
        if ($Node->updateAllNodesPaths()) {
            $messageFlash = __d('croogo', 'Paths updated.');
            $class = 'success';
        } else {
            $messageFlash = __d('croogo', 'Something went wrong while updating paths.' . "\n" . 'Please try again');
            $class = 'error';
        }

        $this->Flash->set($messageFlash, ['element' => 'flash', 'param' => compact('class')]);
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Admin delete meta
     *
     * @param integer $id
     * @return void
     * @access public
     * @deprecated Use MetaController::admin_delete_meta()
     */
    public function delete_meta($id = null)
    {
        $success = false;
        $Node = $this->{$this->modelClass};
        if ($id != null && $Node->Meta->delete($id)) {
            $success = true;
        } else {
            if (!$Node->Meta->exists($id)) {
                $success = true;
            }
        }

        $success = array('success' => $success);
        $this->set(compact('success'));
        $this->set('_serialize', 'success');
    }

    /**
     * Admin add meta
     *
     * @return void
     * @access public
     * @deprecated Use MetaController::admin_add_meta()
     */
    public function add_meta()
    {
        $this->layout = 'ajax';
    }

    /**
     * Admin process
     *
     * @return void
     * @access public
     */
    public function process()
    {
        list($action, $ids) = $this->BulkProcess->getRequestVars($this->Nodes->alias());

        $options = array(
            'multiple' => array('copy' => false),
            'messageMap' => array(
                'delete' => __d('croogo', 'Nodes deleted'),
                'publish' => __d('croogo', 'Nodes published'),
                'unpublish' => __d('croogo', 'Nodes unpublished'),
                'promote' => __d('croogo', 'Nodes promoted'),
                'unpromote' => __d('croogo', 'Nodes unpromoted'),
                'copy' => __d('croogo', 'Nodes copied'),
            ),
        );
        $this->BulkProcess->process($this->Nodes, $action, $ids, $options);
    }

    public function beforePaginate(Event $event)
    {
        /** @var \Cake\ORM\Query $query */
        $query = $event->subject()->query;

        $query->contain([
            'Users'
        ]);

        $types = $this->Nodes->Taxonomies->Vocabularies->Types->find('all');
        $typeAliases = collection($types)->extract('alias');
        $this->set([
            'types' => $types,
            'typeAliases' => $typeAliases
        ]);

        $nodeTypes = $this->Nodes->Taxonomies->Vocabularies->Types->find('list', [
            'keyField' => 'alias',
            'valueField' => 'title'
        ])->toArray();
        $this->set('nodeTypes', $nodeTypes);

        if ($this->request->query('type')) {
            $type = $this->Nodes->Taxonomies->Vocabularies->Types->findByAlias($this->request->query('type'))
                ->first();
            $this->set('type', $type);
        }

        if (!empty($this->request->query('links')) || isset($this->request->query['chooser'])) {
            $this->Crud->action()->view('chooser');
        }
    }

    public function beforeLookup(Event $event)
    {
        /** @var \Cake\ORM\Query $query */
        $query = $event->subject()->query;

        $query->contain([
            'Users'
        ]);
    }

    public function beforeCrudRender(Event $event)
    {
        if (!isset($event->subject()->entity)) {
            return;
        }

        $entity = $event->subject()->entity;

        switch ($this->request->action) {
            case 'add':
                $typeAlias = $this->request->param('pass.0');
                break;
            case 'edit':
                $typeAlias = $entity->type;
                break;
            default:
                return;
        }

        $type = $this->Nodes->Taxonomies->Vocabularies->Types->findByAlias($typeAlias)->contain('Vocabularies')->first();

        $this->set('type', $type);

        $this->_setCommonVariables($type);
    }

    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Crud.beforePaginate' => 'beforePaginate',
            'Crud.beforeLookup' => 'beforeLookup',
            'Crud.beforeRender' => 'beforeCrudRender',
        ];
    }

    /**
     * Set common form variables to views
     * @param array|Type $type Type data
     */
    protected function _setCommonVariables(Type $type)
    {
        if (isset($this->Taxonomies)) {
            $this->Taxonomies->prepareCommonData($type);
        }
        $roles = $this->Nodes->Users->Roles->find('list');
        $parents = $this->Nodes->find('list')->where([
            'type' => $type->id
        ])->toArray();
        $users = $this->Nodes->Users->find('list')->toArray();
        $this->set(compact('roles', 'parents', 'users'));
    }
}
