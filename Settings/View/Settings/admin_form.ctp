<?php
$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb($this->Html->icon('home'), '/admin')
	->addCrumb(__d('croogo', 'Settings'), array(
		'admin' => true,
		'plugin' => 'settings',
		'controller' => 'settings',
		'action' => 'index',
	));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html->addCrumb($this->request->data['Setting']['key'], '/' . $this->request->url);
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
}

$this->append('form-start', $this->Form->create('Setting', array(
	'class' => 'protected-form',
)));

$this->start('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'Settings'), '#setting-basic');
	echo $this->Croogo->adminTab(__d('croogo', 'Misc'), '#setting-misc');
$this->end();

$this->start('tab-content');
	echo $this->Html->tabStart('setting-basic') .
		$this->Form->input('id') .
		$this->Form->input('key', array(
			'help' => __d('croogo', "e.g., 'Site.title'"),
			'label' => __d('croogo', 'Key'),
		)) .
		$this->Form->input('value', array(
			'label' => __d('croogo', 'Value'),
		)) .
	$this->Html->tabEnd();

	echo $this->Html->tabStart('setting-misc') .
		$this->Form->input('title', array(
			'label' => __d('croogo', 'Title'),
		)) .
		$this->Form->input('description', array(
			'label' => __d('croogo', 'Description'),
		)) .
		$this->Form->input('input_type', array(
			'label' => __d('croogo', 'Input Type'),
			'help' => __d('croogo', "e.g., 'text' or 'textarea'"),
		)) .
		$this->Form->input('editable', array(
			'label' => __d('croogo', 'Editable'),
		)) .
		$this->Form->input('params', array(
			'label' => __d('croogo', 'Params'),
		)) .
	$this->Html->tabEnd();

	echo $this->Croogo->adminTabs();
$this->end();

$this->start('panels');
	echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
		$this->Form->button(__d('croogo', 'Save'), array('button' => 'default')) .
		$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array(
			'button' => 'danger')
		) .
	$this->Html->endBox();

	echo $this->Croogo->adminBoxes();
$this->end();

$this->append('form-end', $this->Form->end());