<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb($this->Html->icon('home'), '/admin')
	->addCrumb(__d('croogo', 'Extensions'), array('plugin' => 'extensions', 'controller' => 'extensions_plugins', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Themes'), array('plugin' => 'extensions', 'controller' => 'extensions_themes', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Upload'), '/' . $this->request->url);

$this->append('form-start', $this->Form->create('Theme', array(
	'url' => array(
		'plugin' => 'extensions',
		'controller' => 'extensions_themes',
		'action' => 'add',
	),
	'type' => 'file',
)));

$this->append('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'Upload'), '#themes-upload');
	echo $this->Croogo->adminTabs();
$this->end();

$this->append('tab-content');
	echo $this->Html->tabStart('themes-upload') .
		$this->Form->input('Theme.file', array(
			'type' => 'file', 'button' => 'default',
		));
	echo $this->Html->tabEnd();

	echo $this->Croogo->adminTabs();
$this->end();

$this->append('panels');
	echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
		$this->Form->button(__d('croogo', 'Upload')) .
		$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('button' => 'danger'));
	echo $this->Html->endBox();

	echo $this->Croogo->adminBoxes();
$this->end();

$this->append('form-end', $this->Form->end());
