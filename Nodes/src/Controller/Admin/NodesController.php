<?php
declare(strict_types=1);

/**
 * Croogo : A CakePHP powered Content Management System (http://www.croogo.org)
 * Copyright (c) Fahad Ibnay Heylaal <contact@fahad19.com> and other contributors
 * Copyright (c) Rachman Chavik <rchavik@gmail.com> and other contributors
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
namespace Croogo\Nodes\Controller\Admin;

use Cake\Event\EventInterface;
use Cake\Utility\Hash;
use Croogo\Taxonomy\Model\Entity\Type;

/**
 * Nodes Controller
 *
 * @property \Crud\Controller\Component\CrudComponent $Crud
 * @property \Croogo\Nodes\Model\Table\NodesTable $Nodes
 * @property \Croogo\Core\Controller\Component\CroogoComponent $Croogo
 * @property \Croogo\Taxonomy\Controller\Component\TaxonomyComponent $Taxonomy
 * @property \Croogo\Meta\Controller\Component\MetaComponent $Meta
 * @property \Croogo\Blocks\Controller\Component\BlocksComponent $BlocksHook
 * @property \Croogo\Acl\Controller\Component\FilterComponent $Filter
 * @property \Acl\Controller\Component\AclComponent $Acl
 * @property \Croogo\Core\Controller\Component\ThemeComponent $Theme
 * @property \Croogo\Acl\Controller\Component\AccessComponent $Access
 * @property \Croogo\Settings\Controller\Component\SettingsComponent $SettingsComponent
 * @property \Croogo\Nodes\Controller\Component\NodesComponent $NodesHook
 * @property \Croogo\Menus\Controller\Component\MenuComponent $Menu
 * @property \Croogo\Users\Controller\Component\LoggedInUserComponent $LoggedInUser
 * @property \Croogo\Core\Controller\Component\BulkProcessComponent $BulkProcess
 * @property \Croogo\Core\Controller\Component\RecaptchaComponent $Recaptcha
 * @property \Search\Controller\Component\SearchComponent $Search
 */
class NodesController extends AppController
{
    /**
     * @return void
     * @throws \Crud\Error\Exception\MissingActionException
     * @throws \Crud\Error\Exception\ActionNotConfiguredException
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Croogo/Core.BulkProcess');
        $this->loadComponent('Croogo/Core.Recaptcha');
        $this->loadComponent('Croogo/Core.BulkProcess');

        if ($this->getRequest()->getParam('action') == 'toggle') {
            $this->Croogo->protectToggleAction();
        }
        $this->Crud->mapAction('hierarchy', 'Crud.Index');

        $this->_setupPrg();
    }

    /**
     * Admin create
     *
     * @return Cake\Http\Response|void
     * @access public
     */
    public function create()
    {
        $types = $this->Nodes->Taxonomies->Vocabularies->Types->find('all', [
            'order' => [
                'Types.alias' => 'ASC',
            ],
        ]);

        if ($types->count() === 1) {
            return $this->redirect(['action' => 'add', $types->first()->getAlias()]);
        }

        $this->set(compact('types'));
    }

    /**
     * Admin update paths
     *
     * @return Cake\Http\Response|void
     * @access public
     */
    public function updatePaths()
    {
        $Node = $this->{$this->getName()};
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
     * Admin process
     *
     * @return Cake\Http\Response|void
     * @access public
     */
    public function process()
    {
        [$action, $ids] = $this->BulkProcess->getRequestVars($this->Nodes->getAlias());

        $options = [
            'multiple' => ['copy' => false],
            'messageMap' => [
                'delete' => __d('croogo', 'Nodes deleted'),
                'publish' => __d('croogo', 'Nodes published'),
                'unpublish' => __d('croogo', 'Nodes unpublished'),
                'promote' => __d('croogo', 'Nodes promoted'),
                'unpromote' => __d('croogo', 'Nodes unpromoted'),
                'copy' => __d('croogo', 'Nodes copied'),
            ],
        ];
        $this->BulkProcess->process($this->Nodes, $action, $ids, $options);
    }

    /**
     * @param Event $event Event
     * @return void
     * @throws \Crud\Error\Exception\MissingActionException
     * @throws \Crud\Error\Exception\ActionNotConfiguredException
     */
    public function beforePaginate(EventInterface $event)
    {
        /** @var \Cake\ORM\Query $query */
        $query = $event->getSubject()->query;

        if (empty($this->getRequest()->getQuery('sort'))) {
            if ($this->getRequest()->getQuery('type')) {
                $this->paginate['order'] = [
                    $this->Nodes->aliasField('lft') => 'ASC',
                ];
            } else {
                $this->paginate['order'] = [
                    $this->Nodes->aliasField('publish_start') => 'DESC',
                ];
            };
        }

        $query->contain([
            'Users'
        ]);

        $types = $this->Nodes->Taxonomies->Vocabularies->Types
            ->find()
            ->where([
                'plugin is' => null,
            ]);
        $typeAliases = collection($types)->extract('alias');
        $query->where([
            'type IN' => $typeAliases->toArray()
        ]);

        $this->set([
            'types' => $types,
            'typeAliases' => $typeAliases
        ]);

        $nodeTypes = $types->combine('alias', 'title')->toArray();
        $this->set('nodeTypes', $nodeTypes);

        if ($this->getRequest()->getQuery('type')) {
            $type = $this->Nodes->Taxonomies->Vocabularies->Types
                ->findByAlias($this->getRequest()->getQuery('type'))
                ->first();
            $this->set('type', $type);

            $this->Nodes->behaviors()->Tree->setConfig('scope', [
                'type' => $type->alias,
            ]);
        }

        if (!empty($this->getRequest()->getQuery('links')) || $this->getRequest()->getQuery('chooser')) {
            $this->viewBuilder()->setLayout('admin_popup');
            /** @var \Crud\Action\IndexAction */
            $action = $this->Crud->action();
            $action->view('chooser');
        }
    }

    /**
     * @param Event $event
     * @return void
     */
    public function beforeLookup(EventInterface $event)
    {
        /** @var \Cake\ORM\Query $query */
        $query = $event->getSubject()->query;

        $query->contain([
            'Users'
        ]);
    }

    /**
     * @param Event $event
     * @return void
     */
    public function beforeCrudRender(EventInterface $event)
    {
        if (!isset($event->getSubject()->entity)) {
            return;
        }

        $entity = $event->getSubject()->entity;

        switch ($this->getRequest()->getParam('action')) {
            case 'add':
                $typeAlias = $this->getRequest()->getParam('pass.0');
                break;
            case 'edit':
                $typeAlias = $entity->type;
                break;
            default:
                return;
        }
        if (!$typeAlias) {
            $typeAlias = 'post';
        }

        $type = $this->Nodes->Taxonomies->Vocabularies->Types
            ->findByAlias($typeAlias)
            ->contain('Vocabularies')
            ->first();

        if (!$type) {
            return;
        }

        $this->set('type', $type);

        $this->_setCommonVariables($type);
    }

    /**
     * @param \Cake\Event\Event $event
     * @return void
     */
    public function beforeCrudFind(EventInterface $event)
    {
        $event->getSubject()->query->contain(['Users', 'Parent']);
    }

    /**
     * @param \Cake\Event\Event $event
     * @return void
     */
    public function beforeCrudSave(EventInterface $event)
    {
        $entity = $event->getSubject()->entity;
        if (($this->getRequest()->getParam('action') === 'add') && ($this->getRequest()->getParam('pass.0'))) {
            $entity->type = $this->getRequest()->getParam('pass.0');
        }

        $this->Crud->action()->setConfig('name', $entity->type);

        $this->Nodes->behaviors()->Tree->setConfig('scope', [
            'type' => $entity->type,
        ]);
    }

    public function beforeCrudRedirect(EventInterface $event)
    {
        if ($this->redirectToSelf($event)) {
            return;
        }
    }

    public function afterCrudSave(EventInterface $event)
    {
        $entityErrors = $event->getSubject()->entity->getErrors();
        if (!empty($entityErrors)) {
            $errors = Hash::flatten($entityErrors);
            $message = [];
            foreach ($errors as $field => $error) {
                $message[] = "$field: $error";
            }
            $this->Crud->action()->setConfig('messages.error.text', implode(', ', $message));
        }
    }

    public function implementedEvents(): array
    {
        return parent::implementedEvents() + [
            'Crud.beforeFind' => 'beforeCrudFind',
            'Crud.beforePaginate' => 'beforePaginate',
            'Crud.beforeLookup' => 'beforeLookup',
            'Crud.beforeRender' => 'beforeCrudRender',
            'Crud.beforeSave' => 'beforeCrudSave',
            'Crud.beforeRedirect' => 'beforeCrudRedirect',
            'Crud.afterSave' => 'afterCrudSave',
        ];
    }

    /**
     * Set common form variables to views
     * @param Type $type Type data
     */
    protected function _setCommonVariables(Type $type)
    {
        if (isset($this->Taxonomy)) {
            $this->Taxonomy->prepareCommonData($type);
        }
        $roles = $this->Nodes->Users->Roles->find('list');
        $parents = $this->Nodes->find('list')->where([
            'type' => $type->alias,
        ])->toArray();
        $users = $this->Nodes->Users->find('list')->toArray();
        $this->set(compact('roles', 'parents', 'users'));
    }

    /**
     * @return \Cake\Http\Response
     * @throws \Exception
     */
    public function toggle()
    {
        return $this->Crud->execute();
    }

    /**
     * @param $id
     * @param string $direction
     * @param string $step
     *
     * @return \Cake\Http\Response|null
     */
    public function move($id, $direction = 'up', $step = '1')
    {
        $node = $this->Nodes->get($id);
        $this->Nodes->behaviors()->Tree->setConfig('scope', [
            'type' => $node->type,
        ]);
        if ($direction == 'up') {
            if ($this->Nodes->moveUp($node)) {
                $this->Flash->success(__d('croogo', 'Content moved up'));

                return $this->redirect($this->referer());
            }
        } else {
            if ($this->Nodes->moveDown($node)) {
                $this->Flash->success(__d('croogo', 'Content moved down'));

                return $this->redirect($this->referer());
            }
        }
    }

    /**
     * @return \Cake\Http\Response
     * @throws \Exception
     */
    public function hierarchy()
    {
        $typeAlias = $this->getRequest()->getQuery('type');
        if ($typeAlias) {
            $type = $this->Nodes->Types->findByAlias($typeAlias)->first();
            $this->set(compact('type'));
        }
        $this->Crud->on('beforePaginate', function (EventInterface $event) {
            $event->getSubject()->query->find('treelist');
        });
        $this->Crud->on('afterPaginate', function (EventInterface $event) {
            $subject = $event->getSubject();
            $nodes = [];
            foreach ($subject->entities as $id => $title) {
                $node = $this->Nodes->find()->where(['Nodes.id' => $id])->first();
                $node->depth = substr_count($title, '_', 0);
                $node->clean();
                $nodes[] = $node;
            }
            $subject->entities = $nodes;
        });

        return $this->Crud->execute();
    }
}
