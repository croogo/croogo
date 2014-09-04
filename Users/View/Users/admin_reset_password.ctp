<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb($this->Html->icon('home'), '/admin')
	->addCrumb(__d('croogo', 'Users'), array('plugin' => 'users', 'controller' => 'users', 'action' => 'index'))
	->addCrumb($this->request->data['User']['name'], array(
		'action' => 'edit', $this->request->data['User']['id'],
	))
	->addCrumb(__d('croogo', 'Reset Password'), '/' . $this->request->url);

$this->set('title_for_layout', __d('croogo', 'Reset Password: %s', $this->request->data['User']['username']));

$this->append('form-start', $this->Form->create('User', array(
	'url' => array(
		'action' => 'reset_password',
	)
)));

$this->append('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'Reset Password'), '#reset-password');
	echo $this->Croogo->adminTabs();
$this->end();

$this->append('tab-content');

	echo $this->Html->tabStart('reset-password') .
		$this->Form->input('id') .
		$this->Form->input('password', array(
			'label' => __d('croogo', 'New Password'),
			'value' => '',
		)) .
		$this->Form->input('verify_password', array(
			'label' => __d('croogo', 'Verify Password'),
			'type' => 'password',
			'value' => '',
		));
	echo $this->Html->tabEnd();

	echo $this->Croogo->adminTabs();
$this->end();

$this->append('panels');
	echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
		$this->Form->button(__d('croogo', 'Reset')) .
		$this->Html->link(
			__d('croogo', 'Cancel'),
			array('action' => 'index'),
			array('button' => 'primary')
		);
	echo $this->Html->endBox();

	echo $this->Croogo->adminBoxes();
$this->end();

$this->append('form-end', $this->Form->end());
