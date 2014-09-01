<?php

$this->extend('/Common/admin_edit');

$this->Html->addCrumb('', '/admin', array('icon' => $_icons['home']))
	->addCrumb(__d('croogo', 'Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Types'), array('plugin' => 'taxonomy', 'controller' => 'types', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html->addCrumb($this->request->data['Type']['title'], '/' . $this->request->url);
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
}

$this->append('form-start', $this->Form->create('Type'));

$this->append('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'Type'), '#type-main');
	echo $this->Croogo->adminTab(__d('croogo', 'Taxonomy'), '#type-taxonomy');
	echo $this->Croogo->adminTab(__d('croogo', 'Comments'), '#type-comments');
	echo $this->Croogo->adminTab(__d('croogo', 'Params'), '#type-params');
	echo $this->Croogo->adminTabs();
$this->end();

$this->append('tab-content');

	echo $this->Html->tabStart('type-main') .
		$this->Form->input('id') .
		$this->Form->input('title', array(
			'label' => __d('croogo', 'Title'),
		)) .
		$this->Form->input('alias', array(
			'label' => __d('croogo', 'Alias'),
		)) .
		$this->Form->input('description', array(
			'label' => __d('croogo', 'Description'),
		)) .
		$this->Form->input('format_use_wysiwyg', array(
			'label' => __d('croogo', 'Enable Wysiwyg Editor'),
		));
	echo $this->Html->tabEnd();

$this->end();

$this->append('tab-content');
	echo $this->Html->tabStart('type-taxonomy').
		$this->Form->input('Vocabulary.Vocabulary', array(
			'label' => __d('croogo', 'Vocabulary'),
		));
	echo $this->Html->tabEnd();
$this->end();

$this->append('tab-content');

	echo $this->Html->tabStart('type-comments').
		$this->Form->input('comment_status', array(
			'type' => 'radio',
			'options' => array(
				'0' => __d('croogo', 'Disabled'),
				'1' => __d('croogo', 'Read only'),
				'2' => __d('croogo', 'Read/Write'),
				),
			'default' => 2,
			'legend' => false,
			'label' => true,
			)) .
		$this->Form->input('comment_approve', array(
			'label' => 'Auto approve comments',
			)) .
		$this->Form->input('comment_spam_protection', array(
			'label' => __d('croogo', 'Spam protection (requires Akismet API key)'),
			)) .
		$this->Form->input('comment_captcha', array(
			'label' => __d('croogo', 'Use captcha? (requires Recaptcha API key)'),
			)) .
		$this->Html->link(__d('croogo', 'You can manage your API keys here.'), array(
			'plugin' => 'settings',
			'controller' => 'settings',
			'action' => 'prefix',
			'Service'
		));
	echo $this->Html->tabEnd();
$this->end();

$this->append('tab-content');

	echo $this->Html->tabStart('type-params') .
		$this->Form->input('Type.params', array(
			'label' => __d('croogo', 'Params'),
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
		) .
		$this->Form->input('format_show_author', array(
			'label' => __d('croogo', 'Show author\'s name'),
		)) .
		$this->Form->input('format_show_date', array(
			'label' => __d('croogo', 'Show date'),
		)) .
		$this->Html->endBox();

	echo $this->Croogo->adminBoxes();
$this->end();

$this->append('form-end', $this->Form->end());
