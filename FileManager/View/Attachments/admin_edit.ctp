<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Attachments'), array('plugin' => 'file_manager', 'controller' => 'attachments', 'action' => 'index'))
	->addCrumb($this->data['Attachment']['title'], '/' . $this->request->url);

$this->append('form-start', $this->Form->create('Attachment', array(
	'url' => array(
		'controller' => 'attachments',
		'action' => 'edit',
	)
)));

$this->append('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'Attachment'), '#attachment-main');
$this->end();

$this->append('tab-content');

	echo $this->Html->tabStart('attachment-main') .
		$this->Form->input('id') .
		$this->Form->input('title', array(
			'label' => __d('croogo', 'Title'),
		)) .
		$this->Form->input('excerpt', array(
			'label' => __d('croogo', 'Caption'),
		)) .
		$this->Form->input('file_url', array(
			'label' => __d('croogo', 'File URL'),
			'value' => Router::url($this->data['Attachment']['path'], true),
			'readonly' => 'readonly',
		)) .
		$this->Form->input('file_type', array(
			'label' => __d('croogo', 'Mime Type'),
			'value' => $this->data['Attachment']['mime_type'],
			'readonly' => 'readonly')
		);
	echo $this->Html->tabEnd();

	echo $this->Croogo->adminTabs();
$this->end();

$this->append('panels');
	$redirect = array('action' => 'index');
	if ($this->Session->check('Wysiwyg.redirect')) {
		$redirect = $this->Session->read('Wysiwyg.redirect');
	}
	echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
		$this->Form->button(__d('croogo', 'Apply'), array('name' => 'apply')) .
		$this->Form->button(__d('croogo', 'Save'), array('button' => 'success')) .
		$this->Html->link(
			__d('croogo', 'Cancel'),
			$redirect,
			array('class' => 'cancel', 'button' => 'danger')
		);
	echo $this->Html->endBox();

	$fileType = explode('/', $this->data['Attachment']['mime_type']);
	$fileType = $fileType['0'];
	if ($fileType == 'image'):
		$imgUrl = $this->Image->resize('/uploads/' . $this->data['Attachment']['slug'], 200, 300, true, array('class' => 'img-polaroid'));
	else:
		$imgUrl = $this->Html->image('/croogo/img/icons/' . $this->Filemanager->mimeTypeToImage($this->data['Attachment']['mime_type'])) . ' ' . $this->data['Attachment']['mime_type'];
	endif;
	$preview = $this->Html->link($imgUrl, $this->data['Attachment']['path'], array(
		'class' => 'thickbox',
	));
	echo $this->Html->beginBox(__d('croogo', 'Preview')) .
		$preview;
	echo $this->Html->endBox();

$this->end();

$this->append('form-end', $this->Form->end());