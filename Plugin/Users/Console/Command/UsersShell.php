<?php

class UsersShell extends AppShell {

	public $uses = array(
		'Users.User',
	);

	public function reset() {
		$username = $this->args[0];
		$password = $this->args[1];

		$user = $this->User->findByUsername($username);
		if (empty($user['User']['id'])) {
			return $this->err(__('User %s not found', $username));
		}
		$this->User->id = $user['User']['id'];
		$this->User->saveField('password', $password);
	}
}
