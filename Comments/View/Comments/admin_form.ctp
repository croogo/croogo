<?php

$this->extend('/Common/admin_edit');

$this->Html->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Comments'), array('plugin' => 'comments', 'controller' => 'comments', 'action' => 'index'))
	->addCrumb($this->request->data['Comment']['id'], '/' . $this->request->url);


$this->append('form-start', $this->Form->create('Comment'));

$this->append('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'Comment'), '#comment-main');
	echo $this->Croogo->adminTabs();
$this->end();

$this->append('tab-content');

	echo $this->Html->tabStart('comment-main') .
		$this->Form->input('id') .
		$this->Form->input('title', array(
			'label' => __d('croogo', 'Title'),
		)) .
		$this->Form->input('body', array(
			'label' => __d('croogo', 'Body'),
		));
	echo $this->Html->tabEnd();

	echo $this->Croogo->adminTabs();

$this->end();

$this->start('panels');
	echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
		$this->Form->button(__d('croogo', 'Save'), array('button' => 'default')) .
		$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('button' => 'danger')) .
		$this->Form->input('status', array(
			'label' => __d('croogo', 'Published'),
		)) .
		$this->Html->endBox();

	echo $this->Html->beginBox(__d('croogo', 'Contact')) .
		$this->Form->input('name', array('label' => __d('croogo', 'Name'))) .
		$this->Form->input('email', array('label' => __d('croogo', 'Email'))) .
		$this->Form->input('website', array('label' => __d('croogo', 'Website'))) .
		$this->Form->input('ip', array(
			'disabled' => 'disabled',
			'label' => __d('croogo', 'Ip'))) .
		$this->Html->endBox();

	echo $this->Croogo->adminBoxes();
$this->end();

$this->append('form-end', $this->Form->end());
