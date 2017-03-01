<?php

use Cake\Routing\Router;

$this->assign('title', __d('croogo', 'Edit file: %s', $path));

$this->extend('Croogo/Core./Common/admin_edit');

$this->Html->addCrumb(__d('croogo', 'File Manager'),
        ['plugin' => 'Croogo/FileManager', 'controller' => 'fileManager', 'action' => 'browse'])
    ->addCrumb(basename($absolutefilepath), $this->request->here());

$this->start('page-heading');
echo $this->element('Croogo/FileManager.admin/breadcrumbs');
$this->end();

$this->append('form-start', $this->Form->create(null));

$this->append('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Edit'), '#filemanager-edit');
$this->end();

$this->append('tab-content');
echo $this->Html->tabStart('filemanager-edit') . $this->Form->input('content', [
        'type' => 'textarea',
        'value' => $content,
        'label' => false,
    ]);
echo $this->Html->tabEnd();
$this->end();

$this->append('panels');
echo $this->Html->beginBox(__d('croogo', 'Publishing'));
echo $this->element('Croogo/Core.admin/buttons', [
    'applyText' => false,
]);
echo $this->Html->endBox();
$this->end();

$this->append('form-end', $this->Form->end());
