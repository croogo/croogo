<?php
declare(strict_types=1);

namespace Croogo\Example\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;

/**
 * Example Component
 *
 * An example hook component for demonstrating hook system.
 *
 * @category Component
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExampleComponent extends Component
{

    /**
     * Called after the Controller::beforeFilter() and before the controller action
     *
     * @param object $event Controller with components to startup
     * @return void
     */
    public function startup(\Cake\Event\EventInterface $event)
    {
        $controller = $this->_registry->getController();
        $controller->set('exampleComponent', 'ExampleComponent startup');
    }

    /**
     * Called after the Controller::beforeRender(), after the view class is loaded, and before the
     * Controller::render()
     *
     * @param object $event Controller with components to beforeRender
     * @return void
     */
    public function beforeRender(\Cake\Event\EventInterface $event)
    {
    }

    /**
     * Called after Controller::render() and before the output is printed to the browser.
     *
     * @param object $event Controller with components to shutdown
     * @return void
     */
    public function shutdown(\Cake\Event\EventInterface $event)
    {
    }
}
