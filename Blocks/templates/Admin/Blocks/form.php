<?php

$this->extend('Croogo/Core./Common/admin_edit');

$this->Breadcrumbs->add(__d('croogo', 'Blocks'), ['action' => 'index']);

if ($this->getRequest()->getParam('action') == 'edit') {
    $this->Breadcrumbs->add(h($block->title), $this->getRequest()->getRequestTarget());
}
if ($this->getRequest()->getParam('action') == 'add') {
    $this->Breadcrumbs->add(__d('croogo', 'Add'), $this->getRequest()->getRequestTarget());
}

$this->append('form-start', $this->Form->create($block, [
    'class' => 'protected-form',
]));

$this->append('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Block'), '#block-basic');
echo $this->Croogo->adminTab(__d('croogo', 'Visibilities'), '#block-visibilities');
echo $this->Croogo->adminTab(__d('croogo', 'Params'), '#block-params');
$this->end();

$this->append('tab-content');

echo $this->Html->tabStart('block-basic') . $this->Form->control('title', [
        'label' => __d('croogo', 'Title'),
        'data-slug' => '#alias',
    ]) . $this->Form->control('alias', [
        'label' => __d('croogo', 'Alias'),
        'help' => __d('croogo', 'unique name for your block'),
    ]) . $this->Form->control('region_id', [
        'label' => __d('croogo', 'Region'),
        'help' => __d('croogo', 'if you are not sure, choose \'none\''),
        'class' => 'c-select',
    ]) . $this->Form->control('body', [
        'label' => __d('croogo', 'Body'),
    ]) . $this->Form->control('class', [
        'label' => __d('croogo', 'Class'),
    ]) . $this->Form->control('element', [
        'label' => __d('croogo', 'Element'),
    ]) . $this->Form->control('cell', [
        'label' => __d('croogo', 'Cell'),
    ]);
echo $this->Html->tabEnd();

echo $this->Html->tabStart('block-visibilities') . $this->Form->control('visibility_paths', [
        'type' => 'stringlist',
        'label' => __d('croogo', 'Visibility Paths'),
        'help' => __d('croogo', 'Enter one URL per line. Leave blank if you want this Block to appear in all pages.'),
    ]);
echo $this->Html->tabEnd();

echo $this->Html->tabStart('block-params') . $this->Form->control('params', [
        'type' => 'stringlist',
        'label' => __d('croogo', 'Params'),
    ]);
echo $this->Html->tabEnd();

$this->end();

$this->append('panels');
echo $this->Html->beginBox(__d('croogo', 'Publishing'));
echo $this->element('Croogo/Core.admin/buttons', ['type' => 'block']);
echo $this->element('Croogo/Core.admin/publishable');
echo $this->Form->control('show_title', [
    'label' => __d('croogo', 'Show title ?'),
]);
echo $this->Html->endBox();

echo $this->Html->beginBox(__d('croogo', 'Access control'));
echo $this->Form->control('visibility_roles', [
    'class' => 'c-select',
    'options' => $roles,
    'multiple' => true,
    'label' => false,
]);
echo $this->Html->endBox();

echo $this->Croogo->adminBoxes();
$this->end();
