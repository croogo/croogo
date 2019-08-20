<?php

use Croogo\Core\Status;

$this->extend('Croogo/Core./Common/admin_edit');

$this->Breadcrumbs->add(__d('croogo', 'Blocks'), ['action' => 'index']);

if ($this->request->getParam('action') == 'edit') {
    $this->Breadcrumbs->add($block->title, $this->request->getRequestTarget());
}
if ($this->request->getParam('action') == 'add') {
    $this->Breadcrumbs->add(__d('croogo', 'Add'), $this->request->getRequestTarget());
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

echo $this->Html->tabStart('block-basic') . $this->Form->input('title', [
        'label' => __d('croogo', 'Title'),
        'data-slug' => '#alias',
    ]) . $this->Form->input('alias', [
        'label' => __d('croogo', 'Alias'),
        'help' => __d('croogo', 'unique name for your block'),
    ]) . $this->Form->input('region_id', [
        'label' => __d('croogo', 'Region'),
        'help' => __d('croogo', 'if you are not sure, choose \'none\''),
        'class' => 'c-select',
    ]) . $this->Form->input('body', [
        'label' => __d('croogo', 'Body'),
    ]) . $this->Form->input('class', [
        'label' => __d('croogo', 'Class'),
    ]) . $this->Form->input('element', [
        'label' => __d('croogo', 'Element'),
    ]) . $this->Form->input('cell', [
        'label' => __d('croogo', 'Cell'),
    ]);
echo $this->Html->tabEnd();

echo $this->Html->tabStart('block-visibilities') . $this->Form->input('visibility_paths', [
        'type' => 'stringlist',
        'label' => __d('croogo', 'Visibility Paths'),
        'help' => __d('croogo', 'Enter one URL per line. Leave blank if you want this Block to appear in all pages.'),
    ]);
echo $this->Html->tabEnd();

echo $this->Html->tabStart('block-params') . $this->Form->input('params', [
        'type' => 'stringlist',
        'label' => __d('croogo', 'Params'),
    ]);
echo $this->Html->tabEnd();

$this->end();

$this->append('panels');
echo $this->Html->beginBox(__d('croogo', 'Publishing'));
echo $this->element('Croogo/Core.admin/buttons', ['type' => 'block']);
echo $this->element('Croogo/Core.admin/publishable');
echo $this->Form->input('show_title', [
    'label' => __d('croogo', 'Show title ?'),
]);
echo $this->Html->endBox();

echo $this->Html->beginBox(__d('croogo', 'Access control'));
echo $this->Form->input('visibility_roles', [
    'class' => 'c-select',
    'options' => $roles,
    'multiple' => true,
    'label' => false,
]);
echo $this->Html->endBox();

echo $this->Croogo->adminBoxes();
$this->end();
