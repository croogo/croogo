<?php

$this->extend('/Common/admin_edit');

$this->Html->addCrumb(__d('croogo', 'Extensions'))
    ->addCrumb(__d('croogo', 'Plugins'),
        ['plugin' => 'Croogo/Extensions', 'controller' => 'extensions_plugins', 'action' => 'index'])
    ->addCrumb(__d('croogo', 'Upload'), $this->request->here());

$this->append('form-start', $this->Form->create(null, [
    'url' => [
        'plugin' => 'Croogo/Extensions',
        'controller' => 'extensions_plugins',
        'action' => 'add',
    ],
    'type' => 'file',
]));

$this->append('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Upload'), '#plugins-upload');
$this->end();

$this->append('tab-content');
echo $this->Html->tabStart('plugins-upload') . $this->Form->input('file', [
        'type' => 'file',
        'class' => 'c-file'
    ]);
echo $this->Html->tabEnd();
$this->end();

$this->append('panels');
echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
    '<div class="clearfix"><div class="pull-left">' .
    $this->Form->button(__d('croogo', 'Upload'), ['button' => 'success']) .
    '</div><div class="pull-right">' .
    $this->Html->link(__d('croogo', 'Cancel'), ['action' => 'index'], ['button' => 'danger']) .
    '</div></div>';
echo $this->Html->endBox();
$this->end();

$this->append('form-end', $this->Form->end());
