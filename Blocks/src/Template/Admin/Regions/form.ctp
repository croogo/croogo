<?php

$this->extend('Croogo/Core./Common/admin_edit');

$this->Breadcrumbs->add(__d('croogo', 'Blocks'), [
        'controller' => 'Blocks',
        'action' => 'index',
    ])
    ->add(__d('croogo', 'Regions'), [
        'controller' => 'Regions',
        'action' => 'index',
    ]);

if ($this->getRequest()->getParam('action ')== 'edit') {
    $this->Breadcrumbs->add(h($region->title), $this->getRequest()->getRequestTarget());
}

if ($this->getRequest()->getParam('action') == 'add') {
    $this->Breadcrumbs->add(__d('croogo', 'Add'), $this->getRequest()->getRequestTarget());
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
