<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Contacts'), array('plugin' => 'contacts', 'controller' => 'contacts', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Messages'), array('plugin' => 'contacts', 'controller' => 'messages', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html->addCrumb($this->request->data['Message']['id'] . ': ' . $this->request->data['Message']['title'], '/' . $this->request->url);
}

$this->append('form-start', $this->Form->create('Message'));

$this->append('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'Message'), '#message-main');
	echo $this->Croogo->adminTabs();
$this->end();

$this->append('tab-content');

	echo $this->Html->tabStart('message-main') .
		$this->Form->input('id') .
		$this->Form->input('name', array(
			'label' => __d('croogo', 'Name'),
		)) .
		$this->Form->input('email', array(
			'label' => __d('croogo', 'Email'),
		)) .
		$this->Form->input('title', array(
			'label' => __d('croogo', 'Title'),
		)) .
		$this->Form->input('body', array(
			'label' => __d('croogo', 'Body'),
		)) .
		$this->Form->input('phone', array(
			'label' => __d('croogo', 'Phone'),
		)) .
		$this->Form->input('address', array(
			'label' => __d('croogo', 'Address'),
		));
	echo $this->Html->tabEnd();

	echo $this->Croogo->adminTabs();
$this->end();

$this->start('panels');
	echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
		$this->Form->button(__d('croogo', 'Save')) .
		$this->Html->link(
			__d('croogo', 'Cancel'),
			array('action' => 'index'),
			array('button' => 'danger')
		);
	echo $this->Html->endBox();

	echo $this->Croogo->adminBoxes();
$this->end();

$this->append('form-end', $this->Form->end());