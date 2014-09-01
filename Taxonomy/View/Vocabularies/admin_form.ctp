<?php
$this->Croogo->adminScript('Taxonomy.vocabularies');
$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => $_icons['home']))
	->addCrumb(__d('croogo', 'Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html
		->addCrumb(__d('croogo', 'Vocabularies'), array('plugin' => 'taxonomy', 'controller' => 'vocabularies', 'action' => 'index', $this->request->data['Vocabulary']['id']))
		->addCrumb($this->request->data['Vocabulary']['title'], '/' . $this->request->url);
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html
		->addCrumb(__d('croogo', 'Vocabularies'), array('plugin' => 'taxonomy', 'controller' => 'vocabularies', 'action' => 'index'))
		->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
}

$this->append('form-start', $this->Form->create('Vocabulary'));
$inputDefaults = $this->Form->inputDefaults();
$inputClass = isset($inputDefaults['class']) ? $inputDefaults['class'] : null;

$this->append('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'Vocabulary'), '#vocabulary-basic');
	echo $this->Croogo->adminTab(__d('croogo', 'Options'), '#vocabulary-options');
	echo $this->Croogo->adminTabs();
$this->end();

$this->append('tab-content');

	echo $this->Html->tabStart('vocabulary-basic') .
		$this->Form->input('id') .
		$this->Form->input('title', array(
			'label' => __d('croogo', 'Title'),
			)) .
		$this->Form->input('alias', array(
			'class' => trim($inputClass . ' alias'),
			'label' => __d('croogo', 'Alias'),
			)) .
		$this->Form->input('description', array(
			'label' => __d('croogo', 'Description'),
			)) .
		$this->Form->input('Type.Type', array(
			'label' => __d('croogo', 'Type'),
		));
	echo $this->Html->tabEnd();

	echo $this->Html->tabStart('vocabulary-options') .
		$this->Form->input('required', array(
			'label' => __d('croogo', 'Required'),
			)) .
		$this->Form->input('multiple', array(
			'label' => __d('croogo', 'Multiple'),
			)) .
		$this->Form->input('tags', array(
			'label' => __d('croogo', 'Tags'),
		));
	echo $this->Html->tabEnd();

	echo $this->Croogo->adminTabs();

$this->end();

$this->start('panels');
	echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
		$this->Form->button(__d('croogo', 'Apply'), array('name' => 'apply')) .
		$this->Form->button(__d('croogo', 'Save'), array('button' => 'success')) .
		$this->Html->link(
			__d('croogo', 'Cancel'),
			array('action' => 'index'),
			array('button' => 'danger')
		);

	echo $this->Html->endBox();

	echo $this->end('panels');

$this->append('form-end', $this->Form->end());
