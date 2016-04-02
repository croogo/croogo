<?php
$this->extend('Croogo/Core./Common/admin_edit');

$this->Croogo->adminScript('Croogo/Taxonomy.terms');

$this->Html->addCrumb(__d('croogo', 'Content'),
    ['plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'index']);

if ($this->request->params['action'] == 'edit') {
    $this->Html->addCrumb(__d('croogo', 'Vocabularies'), ['controller' => 'Vocabularies', 'action' => 'index'])
        ->addCrumb($vocabulary->title, ['action' => 'index', $vocabulary->id])
        ->addCrumb($term->title);
}

if ($this->request->params['action'] == 'add') {
    $this->assign('title', __d('croogo', '%s: Add Term', $vocabulary->title));

    $this->Html->addCrumb(__d('croogo', 'Vocabularies'),
        ['controller' => 'Vocabularies', 'action' => 'index', $vocabulary->id])
        ->addCrumb($vocabulary->title, ['action' => 'index'])
        ->addCrumb(__d('croogo', 'Add'));
}

$this->set('cancelUrl', ['action' => 'index', $vocabularyId]);

$this->assign('form-create', $this->Form->create($term));

$this->start('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Term'), '#term-basic');
$this->end();
$this->start('tab-content');
echo $this->Html->beginTabPane('term-basic');
echo $this->Form->input('title', [
    'label' => __d('croogo', 'Title'),
    'data-slug' => '#slug',
]);
echo $this->Form->input('slug', [
    'label' => __d('croogo', 'Slug'),
]);

echo $this->Form->input('taxonomies.0.parent_id', [
    'options' => $parentTree,
    'empty' => '(no parent)',
    'label' => __d('croogo', 'Parent'),
    'class' => 'c-select',
]);
echo $this->Form->hidden('taxonomies.0.id');
echo $this->Form->input('description', [
    'label' => __d('croogo', 'Description'),
]);
echo $this->Html->endTabPane();
$this->end();
$this->assign('form-end', $this->Form->end());
