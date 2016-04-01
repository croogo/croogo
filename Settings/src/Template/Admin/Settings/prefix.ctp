<?php

use Cake\Utility\Inflector;

$this->extend('Croogo/Core./Common/admin_edit');

$this->Html->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Settings'), array('plugin' => 'Croogo/Settings', 'controller' => 'Settings', 'action' => 'index'))
	->addCrumb($prefix, '/' . $this->request->url);

$this->assign('form-start', $this->Form->create('Settings', array(
	'url' => array(
		'controller' => 'Settings',
		'action' => 'prefix',
		$prefix,
	),
	'class' => 'protected-form',
)));

$this->append('tab-heading');
	echo $this->Croogo->adminTab($prefix, '#settings-main');
	echo $this->Croogo->adminTabs();
$this->end();

$this->append('tab-content');
	echo $this->Html->tabStart('settings-main');
		foreach ($settings as $setting) :
			if (!empty($setting['Params']['tab'])) {
				continue;
			}
			$keyE = explode('.', $setting->key);
			$keyTitle = Inflector::humanize($keyE['1']);

			$label = ($setting->title != null) ? $setting->title : $keyTitle;

			echo $this->SettingsForm->input($setting, $label);
		endforeach;

	echo $this->Html->tabEnd();
	echo $this->Croogo->adminTabs();
$this->end();

$this->start('panels');
	echo $this->Html->beginBox(__d('croogo', 'Saving'));
		echo $this->Form->button(__d('croogo', 'Save'));
		echo $this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('class' => 'btn btn-danger'));
	echo $this->Html->endBox();

	echo $this->Croogo->adminBoxes();
$this->end();

$this->assign('form-end', $this->Form->end());
