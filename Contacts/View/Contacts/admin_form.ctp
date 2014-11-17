<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Contacts'), array('controller' => 'contacts', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html->addCrumb($this->request->data['Contact']['title']);
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
}

$this->append('form-start', $this->Form->create('Contact'));

$this->append('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'Contact'), '#contact-basic');
	echo $this->Croogo->adminTab(__d('croogo', 'Details'), '#contact-details');
	echo $this->Croogo->adminTab(__d('croogo', 'Message'), '#contact-message');
	echo $this->Croogo->adminTabs();
$this->end();

$this->append('tab-content');

	echo $this->Html->tabStart('contact-basic') .
		$this->Form->input('id') .
		$this->Form->input('title', array(
			'label' => __d('croogo', 'Title'),
		)) .
		$this->Form->input('alias', array(
			'label' => __d('croogo', 'Alias'),
		)) .
		$this->Form->input('email', array(
			'label' => __d('croogo', 'Email')
		)) .
		$this->Form->input('body', array(
			'label' => __d('croogo', 'Body'),
		));
	echo $this->Html->tabEnd();

	echo $this->Html->tabStart('contact-details') .
		$this->Form->input('name', array(
			'label' => __d('croogo', 'Name'),
		)) .
		$this->Form->input('position', array(
			'label' => __d('croogo', 'Position'),
		)) .
		$this->Form->input('address', array(
			'label' => __d('croogo', 'Address'),
		)) .
		$this->Form->input('address2', array(
			'label' => __d('croogo', 'Address2'),
		)) .
		$this->Form->input('state', array(
			'label' => __d('croogo', 'State'),
		)) .
		$this->Form->input('country', array(
			'label' => __d('croogo', 'Country'),
		)) .
		$this->Form->input('postcode', array(
			'label' => __d('croogo', 'Post Code'),
		)) .
		$this->Form->input('phone', array(
			'label' => __d('croogo', 'Phone'),
		)) .
		$this->Form->input('fax', array(
			'label' => __d('croogo', 'Fax'),
		));
	echo $this->Html->tabEnd();

	echo $this->Html->tabStart('contact-message') .
		$this->Form->input('message_status', array(
			'label' => __d('croogo', 'Let users leave a message'),
		)) .
		$this->Form->input('message_archive', array(
			'label' => __d('croogo', 'Save messages in database'),
		)) .
		$this->Form->input('message_notify', array(
			'label' => __d('croogo', 'Notify by email instantly'),
		)) .
		$this->Form->input('message_spam_protection', array(
			'label' => __d('croogo', 'Spam protection (requires Akismet API key)'),
		)) .
		$this->Form->input('message_captcha', array(
			'label' => __d('croogo', 'Use captcha? (requires Recaptcha API key)'),
		));

		echo $this->Html->link(__d('croogo', 'You can manage your API keys here.'), array(
			'plugin' => 'settings',
			'controller' => 'settings',
			'action' => 'prefix',
			'Service',
		));
	echo $this->Html->tabEnd();

	echo $this->Croogo->adminTabs();
$this->end();

$this->append('panels');
	echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
		$this->Form->button(__d('croogo', 'Apply'), array('name' => 'apply')) .
		$this->Form->button(__d('croogo', 'Save'), array('button' => 'success')) .
		$this->Html->link(
			__d('croogo', 'Cancel'),
			array('action' => 'index'),
			array('button' => 'danger')
		) .
		$this->Form->input('status', array(
			'label' => __d('croogo', 'Published'),
		));
	echo $this->Html->endBox();

	echo $this->Croogo->adminBoxes();
$this->end();

$this->append('form-end', $this->Form->end());
