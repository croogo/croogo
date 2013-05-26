<?php
/**
 * Example Event Handler
 *
 * PHP version 5
 *
 * @category Event
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExampleEventHandler extends Object implements CakeEventListener {

/**
 * implementedEvents
 *
 * @return array
 */
	public function implementedEvents() {
		return array(
			'Controller.Users.adminLoginSuccessful' => array(
				'callable' => 'onAdminLoginSuccessful',
			),
			'Helper.Layout.beforeFilter' => array(
				'callable' => 'onLayoutBeforeFilter',
			),
			'Helper.Layout.afterFilter' => array(
				'callable' => 'onLayoutAfterFilter',
			),
		);
	}

/**
 * onAdminLoginSuccessful
 *
 * @param CakeEvent $event
 * @return void
 */
	public function onAdminLoginSuccessful($event) {
		$Controller = $event->subject();
		$message = sprintf('Welcome %s.  Have a nice day', $Controller->Auth->user('name'));
		$Controller->Session->setFlash($message);
		$Controller->redirect(array(
			'admin' => true,
			'plugin' => 'example',
			'controller' => 'example',
			'action' => 'index',
		));
	}

/**
 * onLayoutBeforeFilter
 *
 * @param CakeEvent $event
 * @return void
 */
	public function onLayoutBeforeFilter($event) {
		$search = 'This is the content of your block.';
		$event->data['content'] = str_replace(
			$search,
			'<p style="font-size: 16px; color: green">' . $search . '</p>',
			$event->data['content']
		);
	}

/**
 * onLayoutAfterFilter
 *
 * @param CakeEvent $event
 * @return void
 */
	public function onLayoutAfterFilter($event) {
		if (strpos($event->data['content'], 'This is') !== false) {
			$event->data['content'] .= '<blockquote>This is added by ExampleEventHandler::onLayoutAfterFilter()</blockquote>';
		}
	}

}
