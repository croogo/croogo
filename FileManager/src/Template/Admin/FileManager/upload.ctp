<?php

$this->extend('Croogo/Core./Common/admin_edit');

$this->Breadcrumbs->add(__d('croogo', 'File Manager'),
    ['plugin' => 'Croogo/FileManager', 'controller' => 'fileManager', 'action' => 'browse'])
    ->add(__d('croogo', 'Upload'), $this->request->getRequestTarget());

$this->start('page-heading');
echo $this->element('Croogo/FileManager.admin/breadcrumbs');
$this->end();

$this->append('form-start', $this->Form->create(null, [
    'type' => 'file'
]));

$this->append('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Upload'), '#filemanager-upload');
$this->end();

$this->append('tab-content');
echo $this->Html->tabStart('filemanager-upload');
echo $this->Form->input('file', [
    'type' => 'file',
    'label' => '',
    'class' => 'file'
]);
echo $this->Html->tabEnd();

echo $this->Croogo->adminTabs();
$this->end();

$this->append('panels');
echo $this->Html->beginBox(__d('croogo', 'Publishing'));
echo $this->element('Croogo/Core.admin/buttons', [
    'saveText' => __d('croogo', 'Upload file'),
    'applyText' => false,
]);
echo $this->Html->endBox();

echo $this->Croogo->adminBoxes();
$this->end();

$this->append('form-end', $this->Form->end());
