<?php
$this->extend('Croogo/Core./Common/admin_edit');

$this->CroogoHtml
	->addCrumb($this->CroogoHtml->icon('home'), '/admin')
	->addCrumb(__d('croogo', 'Settings'), array(
		'plugin' => 'Croogo/Settings',
		'controller' => 'Settings',
		'action' => 'index',
	));

if ($this->request->param('action') == 'edit') {
	$this->CroogoHtml->addCrumb($setting->key, '/' . $this->request->url);
}

if ($this->request->param('action') == 'add') {
	$this->CroogoHtml->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
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
	echo $this->CroogoHtml->beginBox(__d('croogo', 'Publishing')) .
		$this->CroogoForm->button(__d('croogo', 'Save'), array('button' => 'default')) .
		$this->CroogoHtml->link(__d('croogo', 'Cancel'), array('action' => 'index'), array(
			'button' => 'danger')
		) .
	$this->CroogoHtml->endBox();

	echo $this->Croogo->adminBoxes();
$this->end();

$this->append('form-end', $this->Form->end());
