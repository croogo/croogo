<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->icon('home')))
	->addCrumb(__d('croogo', 'Attachments'), array('plugin' => 'file_manager', 'controller' => 'attachments', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Upload'), '/' . $this->request->url);

$formUrl = array('controller' => 'attachments', 'action' => 'add');
if (isset($this->request->params['named']['editor'])) {
	$formUrl['editor'] = 1;
}

$this->append('form-start', $this->Form->create('Attachment', array(
	'url' => $formUrl,
	'type' => 'file',
)));

$this->append('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'Upload'), '#attachment-upload');
$this->end();

$this->append('tab-content');
	echo $this->Html->tabStart('attachment-upload') .
		$this->Form->input('file', array(
			'type' => 'file',
			'label' => __d('croogo', 'Upload'),
		));
	echo $this->Html->tabEnd();

	echo $this->Croogo->adminTabs();
$this->end();

$this->start('panels');
	$redirect = array('action' => 'index');
	if ($this->Session->check('Wysiwyg.redirect')) {
		$redirect = $this->Session->read('Wysiwyg.redirect');
	}
	echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
		$this->Form->button(__d('croogo', 'Upload')) .
		$this->Html->link(__d('croogo', 'Cancel'), $redirect, array('button' => 'danger'));
	echo $this->Html->endBox();
	echo $this->Croogo->adminBoxes();
$this->end();

$this->append('form-end', $this->Form->end());