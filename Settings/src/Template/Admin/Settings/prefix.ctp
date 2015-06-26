<?php

use Cake\Utility\Inflector;

$this->extend('Croogo/Core./Common/admin_edit');

$this->CroogoHtml->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Settings'), array('plugin' => 'Croogo/Settings', 'controller' => 'Settings', 'action' => 'index'))
	->addCrumb($prefix, '/' . $this->request->url);

$this->assign('form-start', $this->CroogoForm->create('Settings', array(
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
	echo $this->CroogoHtml->tabStart('settings-main');
		foreach ($settings as $setting) :
			if (!empty($setting['Params']['tab'])) {
				continue;
			}
			$keyE = explode('.', $setting->key);
			$keyTitle = Inflector::humanize($keyE['1']);

			$label = ($setting->title != null) ? $setting->title : $keyTitle;

			echo $this->SettingsForm->input($setting, $label);
		endforeach;

	echo $this->CroogoHtml->tabEnd();
	echo $this->Croogo->adminTabs();
$this->end();

$this->start('panels');
	echo $this->CroogoHtml->beginBox(__d('croogo', 'Saving'));
		echo $this->CroogoForm->button(__d('croogo', 'Save'));
		echo $this->CroogoHtml->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('class' => 'btn btn-danger'));
	echo $this->CroogoHtml->endBox();

	echo $this->Croogo->adminBoxes();
$this->end();

$this->assign('form-end', $this->CroogoForm->end());
