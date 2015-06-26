<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Extensions'), array('plugin' => 'extensions', 'controller' => 'extensions_plugins', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Locales'), array('plugin' => 'extensions', 'controller' => 'extensions_locales', 'action' => 'index'))
	->addCrumb($this->request->params['pass'][0], '/' . $this->request->url);

$this->append('form-start', $this->Form->create('Locale', array(
	'url' => array(
		'plugin' => 'extensions',
		'controller' => 'extensions_locales',
		'action' => 'edit',
		$locale
	),
)));

$this->append('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'Content'), '#locale-content');
	echo $this->Croogo->adminTabs();
$this->end();

$this->append('tab-content');
	echo $this->Html->tabStart('locale-content') .
		$this->Form->input('Locale.content', array(
			'label' => __d('croogo', 'Content'),
			'data-placement' => 'top',
			'value' => $content,
			'type' => 'textarea',
		));
	echo $this->Html->tabEnd();

	echo $this->Croogo->adminTabs();
$this->end();

$this->append('panels');
	echo $this->Html->beginBox(__d('croogo', 'Actions')) .
		$this->Form->button(__d('croogo', 'Save')) .
		$this->Html->link(__d('croogo', 'Cancel'),
			array('action' => 'index'),
			array('button' => 'danger')
		);
	echo $this->Html->endBox();

	echo $this->Croogo->adminBoxes();
$this->end();

$this->append('form-end', $this->Form->end());
