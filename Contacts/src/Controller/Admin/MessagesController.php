<?php
declare(strict_types=1);

namespace Croogo\Contacts\Controller\Admin;

use Cake\Event\Event;

/**
 * Messages Controller
 *
 * @category Contacts.Controller
 * @package  Croogo.Contacts.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 *
 * @property \Croogo\Core\Controller\Component\BulkProcessComponent $BulkProcess
 * @property \Croogo\Contacts\Model\Table\MessagesTable $Messages
 * @property \Croogo\Core\Controller\Component\CroogoComponent $Croogo
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
 * @property \Croogo\Taxonomy\Controller\Component\TaxonomyComponent $Taxonomy
 * @property \Crud\Controller\Component\CrudComponent $Crud
 * @property \Search\Controller\Component\SearchComponent $Search
 */
class MessagesController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();

        $this->_setupPrg();

        $this->_loadCroogoComponents(['BulkProcess']);

        $this->Crud->setConfig('actions.index', [
            'searchFields' => [
                'search', 'created' => ['type' => 'date'],
            ],
        ]);
    }

    /**
     * Admin process
     *
     * @return \Cake\Http\Response|void
     * @access public
     */
    public function process()
    {
        $Messages = $this->Messages;
        list($action, $ids) = $this->BulkProcess->getRequestVars($Messages->getAlias());

        $messageMap = [
            'delete' => __d('croogo', 'Messages deleted'),
            'read' => __d('croogo', 'Messages marked as read'),
            'unread' => __d('croogo', 'Messages marked as unread'),
        ];

        return $this->BulkProcess->process($Messages, $action, $ids, [
            'messageMap' => $messageMap,
        ]);
    }

    public function beforePaginate(Event $event)
    {
        $query = $event->getSubject()->query;

        $query->contain([
            'Contacts'
        ]);
    }

    public function beforeCrudRedirect(Event $event)
    {
        if ($this->redirectToSelf($event)) {
            return;
        }
    }

    public function implementedEvents(): array
    {
        return parent::implementedEvents() + [
            'Crud.beforePaginate' => 'beforePaginate',
            'Crud.beforeRedirect' => 'beforeCrudRedirect',
        ];
    }

    public function index()
    {
        $this->Crud->on('beforePaginate', function (Event $event) {
            $query = $event->getSubject()->query;
            if (empty($this->getRequest()->getQuery('sort'))) {
                $query->order([
                    $this->Messages->aliasField('created') => 'desc',
                ]);
            }
        });

        return $this->Crud->execute();
    }
}
