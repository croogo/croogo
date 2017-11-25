<?php
$this->extend('Croogo/Core./Common/admin_edit');

$this->Breadcrumbs
    ->add(__d('croogo', 'Settings'), [
        'plugin' => 'Croogo/Settings',
        'controller' => 'Settings',
        'action' => 'index',
    ]);

if ($this->request->param('action') == 'edit') {
    $this->Breadcrumbs->add($setting->key, $this->request->getRequestTarget());
}

if ($this->request->param('action') == 'add') {
    $this->Breadcrumbs->add(__d('croogo', 'Add'), $this->request->getRequestTarget());
}

$this->append('form-start', $this->Form->create($setting, [
    'class' => 'protected-form',
]));

$this->start('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Settings'), '#setting-basic');
echo $this->Croogo->adminTab(__d('croogo', 'Misc'), '#setting-misc');
$this->end();

$this->start('tab-content');
echo $this->Html->tabStart('setting-basic') . $this->Form->input('key', [
        'help' => __d('croogo', "e.g., 'Site.title'"),
        'label' => __d('croogo', 'Key'),
    ]) . $this->Form->input('value', [
        'label' => __d('croogo', 'Value'),
    ]) . $this->Html->tabEnd();

echo $this->Html->tabStart('setting-misc') . $this->Form->input('title', [
        'label' => __d('croogo', 'Title'),
    ]) . $this->Form->input('description', [
        'label' => __d('croogo', 'Description'),
    ]) . $this->Form->input('input_type', [
        'label' => __d('croogo', 'Input Type'),
        'help' => __d('croogo', "e.g., 'text' or 'textarea'"),
    ]) . $this->Form->input('editable', [
        'label' => __d('croogo', 'Editable'),
    ]) . $this->Form->input('params', [
        'label' => __d('croogo', 'Params'),
    ]) . $this->Html->tabEnd();

$this->end();
