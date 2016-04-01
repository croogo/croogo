<?php

$this->extend('Croogo/Core./Common/admin_edit');

$this->Html
	->addCrumb($this->Html->icon('home'), '/admin')
	->addCrumb(__d('croogo', 'Users'), array('plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'index'));

if ($this->request->param('action') == 'edit') {
	$this->Html->addCrumb($user->name, array(
		'plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'edit',
		$user->id
	));
	$this->set('title_for_layout', __d('croogo', 'Edit user %s', $user->username));
} else {
	$this->set('title_for_layout', __d('croogo', 'New user'));
}

if ($this->request->param('action') == 'add') {
	$this->Html->addCrumb(__d('croogo', 'Add'), array('plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'add'));
}

$this->start('actions');
if ($this->request->param('action') == 'edit'):
	echo $this->Croogo->adminAction(__d('croogo', 'Reset password'), array('action' => 'reset_password', $user->id));
endif;
$this->end();

$this->append('form-start', $this->Form->create($user, array(
	'fieldAccess' => array(
		'User.role_id' => 1,
	),
	'class' => 'protected-form',
)));

$this->append('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'User'), '#user-main');
	echo $this->Croogo->adminTabs();
$this->end();

$this->append('tab-content');

	echo $this->Html->tabStart('user-main');
		echo $this->Form->input('id');
		echo $this->Form->input('role_id', array('label' => __d('croogo', 'Role')));
		$this->Form->templates(array(
			'class' => 'span10',
		));
		echo $this->Form->input('username', array(
			'label' => __d('croogo', 'Username'),
		));
		echo $this->Form->input('name', array(
			'label' => __d('croogo', 'Name'),
		));
		echo $this->Form->input('email', array(
			'label' => __d('croogo', 'Email'),
		));
		echo $this->Form->input('website', array(
			'label' => __d('croogo', 'Website'),
		));
		echo $this->Form->input('timezone', array(
			'type' => 'select',
			'empty' => true,
			'options' => DateTimeZone::listIdentifiers(),
			'label' => __d('croogo', 'Timezone'),
		));
	echo $this->Html->tabEnd();

	echo $this->Croogo->adminTabs();
$this->end();

$this->append('panels');
	echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
		$this->Form->button(__d('croogo', 'Apply'), array('name' => 'apply')) .
		$this->Form->button(__d('croogo', 'Save'), array('button' => 'success')) .
		$this->Html->link(
			__d('croogo', 'Cancel'), array('action' => 'index'),
			array('button' => 'danger'));

	if ($this->request->param('action') == 'add'):

		echo $this->Form->input('notification', array(
				'label' => __d('croogo', 'Send Activation Email'),
				'type' => 'checkbox',
				'class' => false,
		));
	endif;

	echo $this->Form->input('status', array(
		'label' => __d('croogo', 'Status'),
	));

	$showPassword = !empty($user->status);
	if ($this->request->param('action') == 'add'):
		$out = $this->Form->input('password', array(
			'label' => __d('croogo', 'Password'),
			'disabled' => !$showPassword,
		));
		$out .= $this->Form->input('verify_password', array(
			'label' => __d('croogo', 'Verify Password'),
			'disabled' => !$showPassword,
			'type' => 'password'
		));

		$this->Form->unlockField('User.password');
		$this->Form->unlockField('User.verify_password');

		echo $this->Html->div(null, $out, array(
			'id' => 'passwords',
			'style' => $showPassword ? '' : 'display: none',
		));
	endif;

	echo $this->Html->endBox();

	echo $this->Croogo->adminBoxes();
$this->end();

$this->append('form-end', $this->Form->end());

$script = <<<EOF
	$('#UserStatus').on('change', function(e) {
		var passwords = $('#passwords');
		var elements = $('input', passwords);
		elements.prop('disabled', !this.checked);
		if (this.checked) {
			passwords.show('fast');
		} else {
			passwords.hide('fast');
		}
	});
	$('#UserNotification').on('change', function(e) {
		var status = $('#UserStatus');
		status.attr('checked', false);
		status.trigger('change').parent().toggle('fast');
	});
EOF;

if ($this->request->params['action'] == 'admin_add'):
	$this->Js->buffer($script);
endif;
