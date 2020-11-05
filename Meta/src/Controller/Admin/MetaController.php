<?php
declare(strict_types=1);

namespace Croogo\Meta\Controller\Admin;

use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use Croogo\Meta\Controller\AppController;

/**
 * Meta Controller
 *
 * @category Meta.Controller
 * @package  Croogo.Meta
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 * @property \Croogo\Meta\Model\Table\MetaTable $Meta
 * @property \Croogo\Core\Controller\Component\CroogoComponent $Croogo
 * @property \Croogo\Blocks\Controller\Component\BlocksComponent $BlocksHook
 * @property \Croogo\Acl\Controller\Component\FilterComponent $Filter
 * @property \Acl\Controller\Component\AclComponent $Acl
 * @property \Croogo\Core\Controller\Component\ThemeComponent $Theme
 * @property \Croogo\Acl\Controller\Component\AccessComponent $Access
 * @property \Croogo\Settings\Controller\Component\SettingsComponent $SettingsComponent
 * @property \Croogo\Nodes\Controller\Component\NodesComponent $NodesHook
 * @property \Croogo\Menus\Controller\Component\MenuComponent $Menu
 * @property \Croogo\Users\Controller\Component\LoggedInUserComponent $LoggedInUser
 * @property \Croogo\Taxonomy\Controller\Component\TaxonomyComponent $Taxonomy
 * @property \Crud\Controller\Component\CrudComponent $Crud
 * @property \Search\Controller\Component\SearchComponent $Search
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

    public function initialize(): void
    {
        parent::initialize();

        $this->components()->unload('Meta');
        unset($this->Meta);
        $this->loadModel('Croogo/Meta.Meta');

        $this->Crud->setConfig('actions.index', [
            'displayFields' => $this->Meta->displayFields(),
            'searchFields' => ['key', 'value'],
            'relatedModels' => false
        ]);
        $this->Crud->setConfig('actions.edit', [
            'editFields' => $this->Meta->editFields(),
            'relatedModels' => false
        ]);
        $this->Crud->setConfig('actions.add', [
            'editFields' => $this->Meta->editFields(),
            'relatedModels' => false
        ]);

        if ($this->getRequest()->getParam('action') == 'deleteMeta') {
            $this->Security->setConfig('validatePost', false);
        }

        $this->_setupPrg();
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->Crud->on('Crud.beforePaginate', function (EventInterface $event) {
            $event->getSubject()->query->where(['model' => '']);
        });
        $this->Crud->on('Crud.beforeSave', function (EventInterface $event) {
            $entity = $event->getSubject()->entity;
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
        if (!$this->getRequest()->is('post')) {
            throw new \Exception('Invalid request method');
        }
        $Meta = TableRegistry::getTableLocator()->get('Croogo/Meta.Meta');
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
