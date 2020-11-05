<?php
/**
 * @var \App\View\AppView $this
 * @var \Croogo\Settings\Model\Entity\Setting $setting
 */
$this->extend('Croogo/Core./Common/admin_edit');

$this->Breadcrumbs
    ->add(__d('croogo', 'Settings'), [
        'plugin' => 'Croogo/Settings',
        'controller' => 'Settings',
        'action' => 'index',
    ]);

if ($this->getRequest()->getParam('action') == 'edit') {
    $this->Breadcrumbs->add(h($setting->key), $this->getRequest()->getRequestTarget());
}

if ($this->getRequest()->getParam('action') == 'add') {
    $this->Breadcrumbs->add(__d('croogo', 'Add'), $this->getRequest()->getRequestTarget());
}

$this->append('form-start', $this->Form->create($setting, [
    'class' => 'protected-form',
]));

$this->start('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Settings'), '#setting-basic');
echo $this->Croogo->adminTab(__d('croogo', 'Misc'), '#setting-misc');
$this->end();

$this->start('tab-content');
echo $this->Html->tabStart('setting-basic') . $this->Form->control('key', [
        'help' => __d('croogo', "e.g., 'Site.title'"),
        'label' => __d('croogo', 'Key'),
    ]) . $this->Form->control('value', [
        'label' => __d('croogo', 'Value'),
    ]) . $this->Html->tabEnd();

echo $this->Html->tabStart('setting-misc') . $this->Form->control('title', [
        'label' => __d('croogo', 'Title'),
    ]) . $this->Form->control('description', [
        'label' => __d('croogo', 'Description'),
    ]) . $this->Form->control('input_type', [
        'label' => __d('croogo', 'Input Type'),
        'help' => __d('croogo', "e.g., 'text' or 'textarea'"),
    ]) . $this->Form->control('editable', [
        'label' => __d('croogo', 'Editable'),
    ]) . $this->Form->control('params', [
        'label' => __d('croogo', 'Params'),
    ]) . $this->Html->tabEnd();

    $this->end();
