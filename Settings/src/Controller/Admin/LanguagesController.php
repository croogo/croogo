<?php

namespace Croogo\Settings\Controller\Admin;

use Cake\Event\Event;

/**
 * Languages Controller
 *
 * @category Settings.Controller
 * @package  Croogo.Settings
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class LanguagesController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        $this->Crud->setConfig('actions.moveUp', [
            'className' => 'Croogo/Core.Admin/MoveUp'
        ]);
        $this->Crud->setConfig('actions.moveDown', [
            'className' => 'Croogo/Core.Admin/MoveDown'
        ]);
        $this->Crud->setConfig('actions.index', [
            'searchFields' => [
                'title',
                'alias',
                'locale',
            ],
        ]);

        $this->_setupPrg();
    }

    /**
     * Admin select
     *
     * @param int $id
     * @param string $modelAlias
     * @return void
     * @access public
     */
    public function select()
    {
        $id = $this->getRequest()->getQuery('id');
        $modelAlias = $this->getRequest()->getQuery('model');
        if ($id == null ||
            $modelAlias == null) {
            return $this->redirect(['action' => 'index']);
        }

        $this->set('title_for_layout', __d('croogo', 'Select a language'));
        $languages = $this->Languages->find('all', [
            'conditions' => [
                'status' => 1,
            ],
            'order' => 'weight ASC',
        ]);
        $this->set(compact('id', 'modelAlias', 'languages'));
    }

    public function index()
    {
        $this->Crud->on('beforePaginate', function (Event $e) {
            if (empty($this->getRequest()->getQuery('sort'))) {
                $e->getSubject()->query
                    ->orderDesc('status');
            }
        });

        return $this->Crud->execute();
    }
}
