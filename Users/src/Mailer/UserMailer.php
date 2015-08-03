<?php

namespace Croogo\Users\Mailer;

use Cake\Core\Configure;
use Cake\Mailer\Mailer;
use Croogo\Users\Model\Entity\User;

class UserMailer extends Mailer
{

	public $layout = 'default';

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

}
