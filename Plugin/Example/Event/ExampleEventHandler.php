<?php

class ExampleEventHandler extends Object implements CakeEventListener {

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

	public function onAdminLoginSuccessful($event) {
		$Controller = $event->subject();
		$message = __('Welcome %s.  Have a nice day', $Controller->Auth->user('name'));
		$Controller->Session->setFlash($message);
		$Controller->redirect(array(
			'admin' => true,
			'plugin' => 'example',
			'controller' => 'example',
			'action' => 'index',
			));
	}

	public function onLayoutBeforeFilter($event) {
		$search = 'This is the content of your block.';
		$event->data['content'] = str_replace(
			$search,
			'<p style="font-size: 16px; color: green">' . $search . '</p>',
			$event->data['content']
		);
	}

	public function onLayoutAfterFilter($event) {
		if (strpos($event->data['content'], 'This is') !== false) {
			$event->data['content'] .= '<blockquote>This is added by ExampleEventHandler::onLayoutAfterFilter()</blockquote>';
		}
	}

}
