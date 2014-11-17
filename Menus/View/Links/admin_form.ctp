<?php

$this->extend('/Common/admin_edit');

$this->Croogo->adminScript('Menus.admin');

$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Menus'), array('plugin' => 'menus', 'controller' => 'menus', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_add') {
	$this->Html
		->addCrumb($menus[$menuId], array(
			'plugin' => 'menus', 'controller' => 'links', 'action' => 'index',
			'?' => array('menu_id' => $menuId))
		)
		->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
	$formUrl = array(
		'controller' => 'links', 'action' => 'add', 'menu' => $menuId
	);
}

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html
		->addCrumb($this->request->data['Menu']['title'], array(
			'plugin' => 'menus', 'controller' => 'links', 'action' => 'index',
			'?' => array('menu_id' => $this->request->data['Menu']['id'])))
		->addCrumb($this->request->data['Link']['title'], '/' . $this->request->url);
	$formUrl = array(
		'controller' => 'links', 'action' => 'edit',
		'?' => array(
			'menu_id' => $menuId,
		),
	);
}

$this->append('form-start', $this->Form->create('Link', array(
	'url' => $formUrl,
	'class' => 'protected-form',
)));
$inputDefaults = $this->Form->inputDefaults();
$inputClass = isset($inputDefaults['class']) ? $inputDefaults['class'] : null;
$linkChooserUrl = $this->Html->url(array(
	'admin' => true,
	'plugin' => 'menus',
	'controllers' => 'links',
	'action' => 'link_chooser',
));

$this->append('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'Link'), '#link-basic');
	echo $this->Croogo->adminTab(__d('croogo', 'Access'), '#link-access');
	echo $this->Croogo->adminTab(__d('croogo', 'Misc.'), '#link-misc');
	echo $this->Croogo->adminTabs();
$this->end();

$this->append('tab-content');

	echo $this->Html->tabStart('link-basic') .
		$this->Form->input('id') .
		$this->Form->input('menu_id', array(
			'label' => __d('croogo', 'Menu'),
			'selected' => $menuId,
		)) .
		$this->Form->input('parent_id', array(
			'label' => __d('croogo', 'Parent'),
			'options' => $parentLinks,
			'empty' => true,
		)) .
		$this->Form->input('title', array(
			'label' => __d('croogo', 'Title'),
		)) .
		$this->Form->input('link', array(
			'label' => __d('croogo', 'Link'),
			'append' => true,
			'addon' => $this->Html->link('', '#link_choosers', array(
				'button' => 'default',
				'icon' => $this->Theme->getIcon('link'),
				'iconSize' => 'small',
				'data-title' => __d('croogo', 'Link Chooser'),
				'data-toggle' => 'modal',
				'data-remote' => $linkChooserUrl,
			)),
		));
	echo $this->Html->tabEnd();

	echo $this->Html->tabStart('link-access') .
		$this->Form->input('Role.Role', array(
			'label' => __d('croogo', 'Role'),
		));
	echo $this->Html->tabEnd();

	echo $this->Html->tabStart('link-misc') .
		$this->Form->input('class', array(
			'label' => __d('croogo', 'Class'),
			'class' => trim($inputClass . ' class'),
		)) .
		$this->Form->input('description', array(
			'label' => __d('croogo', 'Description'),
		)) .
		$this->Form->input('rel', array(
			'label' => __d('croogo', 'Rel'),
		)) .
		$this->Form->input('target', array(
			'label' => __d('croogo', 'Target'),
		)) .
		$this->Form->input('params', array(
			'label' => __d('croogo', 'Params'),
		));
	echo $this->Html->tabEnd();

	echo $this->Croogo->adminTabs();

$this->end();

$this->start('panels');
	echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
		$this->Form->button(__d('croogo', 'Apply'), array('name' => 'apply')) .
		$this->Form->button(__d('croogo', 'Save'), array('button' => 'success')) .
		$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index', '?' => array('menu_id' => $menuId)), array('button' => 'danger')) .
		$this->Form->input('status', array(
			'type' => 'radio',
			'legend' => false,
			'default' => CroogoStatus::UNPUBLISHED,
			'options' => $this->Croogo->statuses(),
		)) .
		$this->Html->div('input-daterange',
			$this->Form->input('publish_start', array(
				'label' => __d('croogo', 'Publish Start'),
				'type' => 'text',
		)) .
		$this->Form->input('publish_end', array(
			'label' => __d('croogo', 'Publish End'),
			'type' => 'text',
		))
	);
	echo $this->Html->endBox();

	echo $this->Croogo->adminBoxes();

$this->end();

$this->append('form-end', $this->Form->end());

$this->append('page-footer');
	echo $this->element('admin/modal', array(
		'id' => 'link_choosers',
		'title' => __d('croogo', 'Choose Link'),
	));
$this->end();
