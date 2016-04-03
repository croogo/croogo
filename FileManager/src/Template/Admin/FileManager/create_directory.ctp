<?php

use Cake\Routing\Router;

$this->assign('title', __d('croogo', 'Create Directory'));
$this->extend('Croogo/Core./Common/admin_edit');

$this->Html->addCrumb(__d('croogo', 'File Manager'),
    ['plugin' => 'Croogo/FileManager', 'controller' => 'fileManager', 'action' => 'browse'])
    ->addCrumb(__d('croogo', 'Create Directory'));

$this->append('form-start', $this->Form->create(null));

$this->start('page-heading');
echo $this->element('Croogo/FileManager.admin/breadcrumbs');
$this->end();

$this->start('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Directory'), '#filemanager-createdir');
$this->end();

$this->append('tab-content');
echo $this->Html->tabStart('filemanager-createdir') . $this->Form->input('name', [
        'type' => 'text',
        'label' => __d('croogo', 'Directory name'),
        'prepend' => $path,
    ]);
echo $this->Html->tabEnd();
$this->end();

$this->start('panels');
echo $this->Html->beginBox(__d('croogo', 'Publishing'));
echo $this->element('Croogo/Core.admin/buttons', ['saveText' => __d('croogo', 'Create directory')]);
echo $this->Html->endBox();

echo $this->Croogo->adminBoxes();
$this->end();

$this->append('form-end', $this->Form->end());
