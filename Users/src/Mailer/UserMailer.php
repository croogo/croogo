<?php

namespace Croogo\Users\Mailer;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Mailer\Mailer;
use Croogo\Users\Model\Entity\User;

class UserMailer extends Mailer
{

	public $layout = 'default';

	public function implementedEvents()
	{
		return [
			'Users.registered' => 'onRegistration'
		];
	}

	public function resetPassword(User $user)
	{
		$this->_email->profile('default');
		$this->_email->to($user->email);
		$this->_email->subject(__d('croogo', '[%s] Reset Password', Configure::read('Site.title')));
		$this->_email->template('Croogo/Users.forgot_password');

		$this->set([
			'user' => $user
		]);
	}

	public function registrationActivation(User $user)
	{
		$this->_email->profile('default');
		$this->_email->to($user->email);
		$this->_email->subject(__d('croogo', '[%s] Please activate your account', Configure::read('Site.title')));
		$this->_email->template('Croogo/Users.register');

		$this->set([
			'user' => $user
		]);
	}

	public function onRegistration(Event $event, User $user)
	{
		$this->send('registrationActivation', [$user]);
	}

}
