<?php

$this->extend('Croogo/Core./Common/admin_edit');

$this->Html->addCrumb(__d('croogo', 'Blocks'), [
        'controller' => 'blocks',
        'action' => 'index',
    ])
    ->addCrumb(__d('croogo', 'Regions'), [
        'controller' => 'regions',
        'action' => 'index',
    ]);

if ($this->request->params['action'] == 'edit') {
    $this->Html->addCrumb($region->title, $this->request->here());
}

if ($this->request->params['action'] == 'add') {
    $this->Html->addCrumb(__d('croogo', 'Add'), $this->request->here());
}

$this->append('form-start', $this->Form->create($region));

$this->append('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Region'), '#region-main');
$this->end();

$this->append('tab-content');

echo $this->Html->tabStart('region-main') . $this->Form->input('title', [
        'label' => __d('croogo', 'Title'),
        'data-slug' => '#alias'
    ]) . $this->Form->input('alias', [
        'label' => __d('croogo', 'Alias'),
    ]);
echo $this->Html->tabEnd();
$this->end();

$this->append('form-end', $this->Form->end());
