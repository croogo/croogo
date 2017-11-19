<?php

namespace Croogo\Nodes\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Nodes Component
 *
 * @category Component
 * @package  Croogo.Nodes.Controller.Component
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class NodesComponent extends Component
{

/**
 * Nodes for layout
 *
 * @var string
 * @access public
 */
    public $nodesForLayout = [];

/**
 * beforeFilter
 *
 * @param Event $event instance of controller
 */
    public function beforeFilter(Event $event)
    {
        $this->controller = $event->subject;
        if (isset($this->controller->Nodes)) {
            $this->Nodes = $this->controller->Nodes;
        } else {
            $this->Nodes = TableRegistry::get('Croogo/Nodes.Nodes');
        }
    }

/**
 * Startup
 *
 * @param Controller $controller instance of controller
 * @return void
 */
    public function startup(Event $event)
    {
        $controller = $event->subject();
        if (($controller->request->param('prefix') !== 'admin') && !isset($controller->request->params['requested'])) {
            $this->nodes();
        }
    }

/**
 * Nodes
 *
 * Nodes will be available in this variable in views: $nodesForLayout
 *
 * @return void
 */
    public function nodes()
    {
        $roleId = $this->controller->Croogo->roleId();

        $nodes = $this->controller->BlocksHook->blocksData['nodes'];
        $_nodeOptions = [
            'find' => 'all',
            'findOptions' => [],
            'conditions' => [],
            'order' => 'Nodes.created DESC',
            'limit' => 5,
        ];

        foreach ($nodes as $alias => $options) {
            $options = Hash::merge($_nodeOptions, $options);
            $options['limit'] = str_replace('"', '', $options['limit']);
            $node = $this->Nodes->find($options['find'], $options['findOptions'])
                ->where($options['conditions'])
                ->order($options['order'])
                ->limit($options['limit'])
                ->applyOptions([
                    'prefix' => 'nodes_' . $alias,
                    'config' => 'croogo_nodes',
                ])->find('byAccess', [
                    'roleId' => $roleId
                ])->find('published');

            $this->nodesForLayout[$alias] = $node;
        }
    }

/**
 * beforeRender
 *
 * @param object $controller instance of controller
 * @return void
 */
    public function beforeRender(Event $event)
    {
        $event->subject()->set('nodesForLayout', $this->nodesForLayout);
    }
}
