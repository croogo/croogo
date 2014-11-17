<?php
$this->extend('/Common/admin_edit');
$this->set('className', 'acl_actions');
$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Users'), array('plugin' => 'users', 'controller' => 'users', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Permissions'), array('plugin' => 'acl', 'controller' => 'acl_permissions'))
	->addCrumb(__d('croogo', 'Actions'), array('plugin' => 'acl', 'controller' => 'acl_actions', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html->addCrumb($this->request->data['Aco']['id'] . ': ' . $this->request->data['Aco']['alias'], '/' . $this->request->url);
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
}

$this->append('form-start', $this->Form->create('Aco', array(
	'url' => array('controller' => 'acl_actions', 'action' => 'add')
)));

$this->start('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'Action'), '#action-main');
	echo $this->Croogo->adminTabs();
$this->end();

$this->start('tab-content');

	echo $this->Html->tabStart('action-main') .
		$this->Form->input('id') .
		$this->Form->input('parent_id', array(
			'options' => $acos,
			'empty' => true,
			'label' => __d('croogo', 'Parent'),
			'help' => __d('croogo', 'Choose none if the Aco is a controller.'),
		)) .
		$this->Form->input('alias', array(
			'label' => __d('croogo', 'Alias'),
		));
	echo $this->Html->tabEnd();

	echo $this->Croogo->adminTabs();
$this->end();

$this->start('panels');
	echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
		$this->Form->button(__d('croogo', 'Apply'), array('name' => 'apply')) .
		$this->Form->button(__d('croogo', 'Save'), array('button' => 'success')) .
		$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('class' => 'cancel btn btn-danger'));
	echo $this->Html->endBox();

	echo $this->Croogo->adminBoxes();
$this->end();

$this->append('form-end', $this->Form->end());
