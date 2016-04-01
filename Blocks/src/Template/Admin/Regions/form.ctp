<?php

$this->extend('Croogo/Core./Common/admin_edit');

$this->Html
	->addCrumb(__d('croogo', 'Blocks'), array(
		'plugin' => 'blocks', 'controller' => 'blocks', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Regions'), array(
		'plugin' => 'blocks', 'controller' => 'regions', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html->addCrumb($this->request->data['Region']['title'], array(
		'plugin' => 'blocks', 'controller' => 'regions', 'action' => 'edit',
		$this->request->params['pass'][0]
	));
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
}

$this->append('form-start', $this->Form->create($region));

$this->append('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'Region'), '#region-main');
	echo $this->Croogo->adminTabs();
$this->end();

$this->append('tab-content');

	echo $this->Html->tabStart('region-main') .
		$this->Form->input('id') .
		$this->Form->input('title', array(
			'label' => __d('croogo', 'Title'),
		)) .
		$this->Form->input('alias', array(
			'label' => __d('croogo', 'Alias'),
		));
	echo $this->Html->tabEnd();

	echo $this->Croogo->adminTabs();
$this->end();

$this->append('panels');
	echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
		$this->Form->button(__d('croogo', 'Apply'), array('name' => 'apply')) .
		$this->Form->button(__d('croogo', 'Save'), array('button' => 'success')) .
		$this->Html->link(__d('croogo', 'Cancel'),
			array('action' => 'index'),
			array('button' => 'danger')
		);
	echo $this->Html->endBox();
	echo $this->Croogo->adminBoxes();
$this->end();

$this->append('form-end', $this->Form->end());
