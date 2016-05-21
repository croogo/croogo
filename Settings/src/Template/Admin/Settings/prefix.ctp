<?php

use Cake\Utility\Inflector;

$this->extend('Croogo/Core./Common/admin_edit');

$this->Html->addCrumb(__d('croogo', 'Settings'),
    ['plugin' => 'Croogo/Settings', 'controller' => 'Settings', 'action' => 'index'])
    ->addCrumb($prefix);

$this->assign('form-start', $this->Form->create(null, [
    'class' => 'protected-form',
]));

$this->append('tab-heading');
echo $this->Croogo->adminTab($prefix, '#settings-main');
$this->end();

$this->append('tab-content');
echo $this->Html->tabStart('settings-main');
foreach ($settings as $setting) :
    if (!empty($setting->params['tab'])) {
        continue;
    }
    $keyE = explode('.', $setting->key);
    $keyTitle = Inflector::humanize($keyE['1']);

    $label = ($setting->title != null) ? $setting->title : $keyTitle;

    echo $this->SettingsForm->input($setting, $label);
endforeach;

echo $this->Html->tabEnd();
$this->end();

$this->assign('form-end', $this->Form->end());
