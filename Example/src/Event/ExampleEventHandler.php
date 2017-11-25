<?php

namespace Croogo\Example\Event;

use Cake\Event\EventListenerInterface;

/**
 * Example Event Handler
 *
 * @category Event
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExampleEventHandler implements EventListenerInterface
{

/**
 * implementedEvents
 *
 * @return array
 */
    public function implementedEvents()
    {
        return [
            'Controller.Users.adminLoginSuccessful' => [
                'callable' => 'onAdminLoginSuccessful',
            ],
            'Helper.Layout.beforeFilter' => [
                'callable' => 'onLayoutBeforeFilter',
            ],
            'Helper.Layout.afterFilter' => [
                'callable' => 'onLayoutAfterFilter',
            ],
        ];
    }

/**
 * onAdminLoginSuccessful
 *
 * @param Event $event
 * @return void
 */
    public function onAdminLoginSuccessful($event)
    {
        $Controller = $event->subject();
        $message = sprintf('Welcome %s.  Have a nice day', $Controller->Auth->user('name'));
        $Controller->Flash->success($message);
        $Controller->redirect([
            'admin' => true,
            'plugin' => 'Croogo/Example',
            'controller' => 'Example',
            'action' => 'index',
        ]);
    }

/**
 * onLayoutBeforeFilter
 *
 * @param Event $event
 * @return void
 */
    public function onLayoutBeforeFilter($event)
    {
        $search = 'This is the content of your block.';
        $data = $event->getData();
        $data['content'] = str_replace(
            $search,
            '<p style="font-size: 16px; color: green">' . $search . '</p>',
            $data['content']
        );
    }

/**
 * onLayoutAfterFilter
 *
 * @param Event $event
 * @return void
 */
    public function onLayoutAfterFilter($event)
    {
        $data = $event->getData();
        if (strpos($data['content'], 'This is') !== false) {
            $data['content'] .= '<blockquote>This is added by ExampleEventHandler::onLayoutAfterFilter()</blockquote>';
        }
    }
}
