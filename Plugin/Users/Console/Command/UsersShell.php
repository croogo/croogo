<?php

class UsersShell extends AppShell {

	public $uses = array(
		'Users.User',
	);

/**
 * getOptionParser
 */
	public function getOptionParser() {
		return parent::getOptionParser()
			->addSubCommand('reset', array(
				'help' => __('Reset user password'),
				'parser' => array(
					'arguments' => array(
						'username' => array(
							'required' => true,
							'help' => __('Username to reset'),
						),
						'password' => array(
							'required' => true,
							'help' => __('New user password'),
						),
					),
				),
			))
			;
	}

/**
 * reset
 */
	public function reset() {
		$username = $this->args[0];
		$password = $this->args[1];

		$user = $this->User->findByUsername($username);
		if (empty($user['User']['id'])) {
			return $this->warn(__('User \'%s\' not found', $username));
		}
		$this->User->id = $user['User']['id'];
		$result = $this->User->saveField('password', $password);
		if ($result) {
			$this->success(__('Password for \'%s\' has been changed', $username));
		}
	}
}
