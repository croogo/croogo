<?php

$this->extend('Croogo/Core./Common/admin_edit');

$this->Breadcrumbs->add(__d('croogo', 'Blocks'), [
        'controller' => 'blocks',
        'action' => 'index',
    ])
    ->add(__d('croogo', 'Regions'), [
        'controller' => 'regions',
        'action' => 'index',
    ]);

if ($this->request->params['action'] == 'edit') {
    $this->Breadcrumbs->add($region->title, $this->request->getRequestTarget());
}

if ($this->request->params['action'] == 'add') {
    $this->Breadcrumbs->add(__d('croogo', 'Add'), $this->request->getRequestTarget());
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
