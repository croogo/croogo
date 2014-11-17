<?php

$this->extend('/Common/admin_edit');

$this->Html->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Settings'), array('plugin' => 'settings', 'controller' => 'settings', 'action' => 'index'))
	->addCrumb($prefix, '/' . $this->request->url);

$this->append('form-start', $this->Form->create('Setting', array(
	'url' => array(
		'controller' => 'settings',
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
		$i = 0;
		foreach ($settings as $setting) :
			if (!empty($setting['Params']['tab'])) {
				continue;
			}
			$keyE = explode('.', $setting['Setting']['key']);
			$keyTitle = Inflector::humanize($keyE['1']);

			$label = ($setting['Setting']['title'] != null) ? $setting['Setting']['title'] : $keyTitle;

			$i = $setting['Setting']['id'];
			echo
				$this->Form->input("Setting.$i.id", array(
					'value' => $setting['Setting']['id'],
				)) .
				$this->Form->input("Setting.$i.key", array(
					'type' => 'hidden', 'value' => $setting['Setting']['key']
				)) .
				$this->SettingsForm->input($setting, __d('croogo', $label), $i);
			$i++;
		endforeach;
		echo $this->Croogo->adminTabs();
	echo $this->Html->tabEnd();

$this->end();

$this->append('panels');
	echo $this->Html->beginBox(__d('croogo', 'Saving')) .
		$this->Form->button(__d('croogo', 'Save')) .
		$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('class' => 'btn btn-danger')) .
	$this->Html->endBox();
	echo $this->Croogo->adminBoxes();
$this->end();

$this->append('form-end', $this->Form->end());
