<?php

use Croogo\Core\Status;

$this->extend('Croogo/Core./Common/admin_edit');
$this->Croogo->adminScript('Croogo/Menus.admin');

$this->CroogoHtml
	->addCrumb('', '/admin', array('icon' => $_icons['home']))
	->addCrumb(__d('croogo', 'Menus'), array('controller' => 'Menus', 'action' => 'index'));

if ($this->request->params['action'] == 'add') {
	$this->CroogoHtml
		->addCrumb($menu->title, array(
			'action' => 'index',
			'?' => array('menu_id' => $menu->id))
		)
		->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
	$formUrl = array(
		'action' => 'add', $menu->id
	);
}

if ($this->request->params['action'] == 'edit') {
	$this->CroogoHtml
		->addCrumb($menu->title, array(
			'action' => 'index',
			'?' => array('menu_id' => $menu->id)))
		->addCrumb($link->title, '/' . $this->request->url);
	$formUrl = array(
		'action' => 'edit',
		'?' => array(
			'menu_id' => $menuId,
		),
	);
}

$this->append('form-start', $this->CroogoForm->create($link, array(
	'url' => $formUrl,
	'class' => 'protected-form',
)));

$inputDefaults = $this->Form->templates();
$inputClass = isset($inputDefaults['class']) ? $inputDefaults['class'] : null;
$linkChooserUrl = $this->Url->build(array(
	'plugin' => 'Croogo/Core',
	'controller' => 'LinkChooser',
	'action' => 'linkChooser',
));

$this->append('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'Link'), '#link-basic');
	echo $this->Croogo->adminTab(__d('croogo', 'Access'), '#link-access');
	echo $this->Croogo->adminTab(__d('croogo', 'Misc.'), '#link-misc');
	echo $this->Croogo->adminTabs();
$this->end();

$this->append('tab-content');

	echo $this->CroogoHtml->tabStart('link-basic');
	echo $this->CroogoForm->input('id');
	echo $this->CroogoForm->input('menu_id', array(
		'selected' => $menuId,
	));
	echo $this->CroogoForm->input('parent_id', array(
		'title' => __d('croogo', 'Parent'),
		'options' => $parentLinks,
		'empty' => true,
	));
	$this->CroogoForm->templates(array(
		'class' => 'span10',
	));
	echo $this->CroogoForm->input('title', array(
		'label' => __d('croogo', 'Title'),
	));

	echo $this->CroogoForm->input('link', array(
		'label' => __d('croogo', 'Link'),
		'div' => 'input text required input-append',
		'after' => $this->CroogoHtml->link('', '#link_choosers', array(
			'button' => 'default',
			'icon' => array('link'),
			'iconSize' => 'small',
			'data-title' => 'Link Chooser',
			'data-toggle' => 'modal',
			'data-remote' => $linkChooserUrl,
		)),
	));
	echo $this->CroogoHtml->tabEnd();

	echo $this->CroogoHtml->tabStart('link-access');
	echo $this->CroogoForm->input('visibility_roles', array(
		'class' => false,
		'options' => $roles,
		'multiple' => true
	));
	echo $this->CroogoHtml->tabEnd();

	echo $this->CroogoHtml->tabStart('link-misc');
	echo $this->CroogoForm->input('class', array(
		'label' => __d('croogo', 'Class'),
		'class' => 'span10 class',
	));
	echo $this->CroogoForm->input('description', array(
		'label' => __d('croogo', 'Description'),
	));
	echo $this->CroogoForm->input('rel', array(
		'label' => __d('croogo', 'Rel'),
	));
	echo $this->CroogoForm->input('target', array(
		'label' => __d('croogo', 'Target'),
	));
	echo $this->CroogoForm->input('params', array(
		'label' => __d('croogo', 'Params'),
	));
	echo $this->CroogoHtml->tabEnd();

	echo $this->Croogo->adminTabs();

$this->end();

$this->start('panels');
	echo $this->CroogoHtml->beginBox(__d('croogo', 'Publishing'));
		echo $this->CroogoForm->button(__d('croogo', 'Apply'), array('name' => 'apply'));
		echo $this->CroogoForm->button(__d('croogo', 'Save'), array('button' => 'success'));
		echo $this->CroogoHtml->link(__d('croogo', 'Cancel'), array('action' => 'index', '?' => array('menu_id' => $menuId)), array('button' => 'danger'));
		echo $this->CroogoForm->input('status', array(
			'type' => 'radio',
			'legend' => false,
			'class' => false,
			'default' => Status::UNPUBLISHED,
			'options' => $this->Croogo->statuses(),
		));
		echo $this->CroogoHtml->div('input-daterange',
			$this->CroogoForm->input('publish_start', array(
				'label' => __d('croogo', 'Publish Start'),
				'type' => 'text',
			)) .
			$this->CroogoForm->input('publish_end', array(
				'label' => __d('croogo', 'Publish End'),
				'type' => 'text',
			))
		);
		echo $this->CroogoHtml->endBox();
	echo $this->Croogo->adminBoxes();

$this->end();

$this->append('form-end', $this->Form->end());

$this->append('page-footer');
	echo $this->element('Croogo/Core.admin/modal', array(
		'id' => 'link_choosers',
		'title' => __d('croogo', 'Choose Link'),
	));
$this->end();
