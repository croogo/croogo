<?php
$this->extend('/Common/admin_edit');

$this->Croogo->adminScript('Taxonomy.terms');

$this->Html
	->addCrumb('', '/admin', array('icon' => $_icons['home']))
	->addCrumb(__d('croogo', 'Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html
		->addCrumb(__d('croogo', 'Vocabularies'), array('plugin' => 'taxonomy', 'controller' => 'vocabularies', 'action' => 'index'))
		->addCrumb($vocabulary['Vocabulary']['title'], array('plugin' => 'taxonomy', 'controller' => 'terms', 'action' => 'index', $vocabulary['Vocabulary']['id']))
		->addCrumb($this->request->data['Term']['title'], '/' . $this->request->url);
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html
		->addCrumb(__d('croogo', 'Vocabularies'), array('plugin' => 'taxonomy', 'controller' => 'vocabularies', 'action' => 'index', $vocabulary['Vocabulary']['id']))
		->addCrumb($vocabulary['Vocabulary']['title'], array('plugin' => 'taxonomy', 'controller' => 'terms', 'action' => 'index'))
		->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
}

$this->Form->create('Term', array('url' => '/' . $this->request->url,));
$inputDefaults = $this->Form->inputDefaults();
$inputClass = isset($inputDefaults['class']) ? $inputDefaults['class'] : null;

$this->append('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'Term'), '#term-basic');
	echo $this->Croogo->adminTabs();
$this->end();

$this->append('tab-content');

	echo $this->Html->tabStart('term-basic') .
		$this->Form->input('Taxonomy.parent_id', array(
			'options' => $parentTree,
			'empty' => true,
			'label' => __d('croogo', 'Parent'),
		)) .
		$this->Form->hidden('Taxonomy.id') .
		$this->Form->hidden('id') .
		$this->Form->input('title', array(
			'label' => __d('croogo', 'Title'),
		)) .
		$this->Form->input('slug', array(
			'label' => __d('croogo', 'Slug'),
			'class' => trim($inputClass . ' slug'),
		)) .
		$this->Form->input('description', array(
			'label' => __d('croogo', 'Description'),
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
			array('action' => 'index', $vocabularyId),
			array('button' => 'danger')
		) .
		$this->Html->endBox();

		echo $this->Croogo->adminBoxes();
$this->end();

$this->append('form-end', $this->Form->end());
