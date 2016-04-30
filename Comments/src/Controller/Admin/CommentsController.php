<?php

namespace Croogo\Comments\Controller\Admin;

use App\Network\Email\Email;
use Cake\Event\Event;
use Croogo\Comments\Model\Entity\Comment;

/**
 * Comments Controller
 *
 * @category Controller
 * @package  Croogo.Comments.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CommentsController extends AppController
{

/**
 * Preset Variable Search
 * @var array
 */
    public $presetVars = true;

/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        if ($this->action == 'admin_edit') {
            $this->Security->disabledFields = ['ip'];
        }
    }

    public function initialize()
    {
        parent::initialize();

        $this->loadCroogoComponents(['Akismet', 'BulkProcess', 'Recaptcha']);
        $this->_setupPrg();
    }

    /**
 * Admin index
 *
 * @return void
 * @access public
 */
    public function index()
    {
        $this->set('title_for_layout', __d('croogo', 'Comments'));
        $this->Prg->commonProcess();

        $query = $this->Comments->find('searchable', $this->Prg->parsedParams())
            ->find('relatedEntity');

        $this->set('comments', $this->paginate($query));
    }

/**
 * Admin edit
 *
 * @param int$id
 * @return void
 * @access public
 */
    public function edit($id = null)
    {
        $this->set('title_for_layout', __d('croogo', 'Edit Comment'));

        if (!$id && empty($this->request->data)) {
            $this->Flash->error(__d('croogo', 'Invalid Comment'));
            return $this->redirect(['action' => 'index']);
        }
        if (!empty($this->request->data)) {
            $comment = $this->Comments->get($id);
            $comment = $this->Comments->patchEntity($block, $this->request->data);
            if ($this->Comments->save($comment)) {
                $this->Flash->success(__d('croogo', 'The Comment has been saved'));
                $this->Croogo->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('crrogo', 'The Comment could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $comment = $this->Comments->get($id);
        }
        $this->set(compact('comment'));
    }

/**
 * Admin delete
 *
 * @param int$id
 * @return void
 * @access public
 */
    public function delete($id = null)
    {
        if (!$id) {
            $this->Flash->error(__d('croogo', 'Invalid id for Comment'));
            return $this->redirect(['action' => 'index']);
        }
        $comment = new Comment(['id' => $id], ['markNew' => false]);
        if ($this->Comments->delete($id)) {
            $this->Flash->success(__d('croogo', 'Comment deleted'));
            return $this->redirect(['action' => 'index']);
        }
    }

/**
 * Admin process
 *
 * @return void
 * @access public
 */
    public function process()
    {
        list($action, $ids) = $this->BulkProcess->getRequestVars($this->Comments->alias());

        $options = [
            'messageMap' => [
                'delete' => __d('croogo', 'Comments deleted'),
                'publish' => __d('croogo', 'Comments published'),
                'unpublish' => __d('croogo', 'Comments unpublished'),
            ]
        ];

        $this->BulkProcess->process($this->Comments, $action, $ids, $options);
    }
}
