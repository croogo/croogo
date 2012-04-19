<?php

class ShopsEventHandler extends Object implements CakeEventListener {

	public function implementedEvents() {
		return array(
			'Controller.Users.activationFailure' => array(
				'callable' => 'onActivationFailure',
				),
			'Controller.Users.activationSuccessful' => array(
				'callable' => 'onActivationSuccessful',
				),
			'Controller.Users.adminLoginSuccessful' => array(
				'callable' => 'onAdminLoginSuccessful',
				),
			'Controller.Users.adminLoginFailure' => array(
				'callable' => 'onAdminLoginFailure',
				),
			'Controller.Users.adminLogoutSuccessful' => array(
				'callable' => 'onAdminLogoutSuccessful',
				),
			'Controller.Users.afterLogout' => array(
				'callable' => 'onAfterLogout',
				),
			'Controller.Users.beforeLogin' => array(
				'callable' => 'onBeforeLogin',
				),
			'Controller.Users.beforeLogout' => array(
				'callable' => 'onBeforeLogout',
				),
			'Controller.Users.loginFailure' => array(
				'callable' => 'onLoginFailure',
				),
			'Controller.Users.loginSuccessful' => array(
				'callable' => 'onLoginSuccessful',
				),
			'Controller.Users.registrationFailure' => array(
				'callable' => 'onRegistrationFailure',
				),
			'Controller.Users.registrationSuccessful' => array(
				'callable' => 'onRegistrationSuccessful',
				),

			'Helper.Layout.beforeFilter' => array(
				'callable' => 'onLayoutBeforeFilter',
				),
			'Helper.Layout.afterFilter' => array(
				'callable' => 'onLayoutAfterFilter',
				),
			);
	}

	public function onActivationFailure($event) {
		return true;
	}

	public function onActivationSuccessful($event) {
		return true;
	}

	public function onAfterLogout($event) {
		return true;
	}

	public function onBeforeLogin($event) {
		return true;
	}

	public function onBeforeLogout($event) {
		return true;
	}

	public function onLoginFailure($event) {
		return true;
	}

	public function onLoginSuccessful($event) {
		return true;
	}

	public function onAdminLoginSuccessful($event) {
		return true;
	}

	public function onAdminLoginFailure($event) {
		return true;
	}

	public function onAdminLogoutSuccessful($event) {
		return true;
	}

	public function onRegistrationFailure($event) {
		return true;
	}

	public function onRegistrationSuccessful($event) {
		return true;
	}

	public function onLayoutBeforeFilter($event) {
		return true;
	}

	public function onLayoutAfterFilter($event) {
		return true;
	}

}
