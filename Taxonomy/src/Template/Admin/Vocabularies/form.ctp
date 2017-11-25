<?php
$this->Croogo->adminScript('Croogo/Taxonomy.vocabularies');

$this->extend('Croogo/Core./Common/admin_edit');

$this->Breadcrumbs->add(__d('croogo', 'Content'),
    ['plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'index']);

if ($this->request->params['action'] == 'edit') {
    $this->assign('title', __d('croogo', 'Edit Vocabulary'));

    $this->Breadcrumbs->add(__d('croogo', 'Vocabularies'), ['action' => 'index', $vocabulary->id])
        ->add($vocabulary->title);
}

if ($this->request->params['action'] == 'add') {
    $this->assign('title', __d('croogo', 'Add Vocabulary'));

    $this->Breadcrumbs->add(__d('croogo', 'Vocabularies'), ['action' => 'index'])
        ->add(__d('croogo', 'Add'), $this->request->getRequestTarget());
}

$this->append('form-start', $this->Form->create($vocabulary, [
    'class' => 'protected-form',
]));

$this->start('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Vocabulary'), '#vocabulary-basic');
$this->end();

$this->start('tab-content');
echo $this->Html->tabStart('vocabulary-basic');
echo $this->Form->input('title', [
    'label' => __d('croogo', 'Title'),
    'data-slug' => '#alias',
]);
echo $this->Form->input('alias', [
    'label' => __d('croogo', 'Alias'),
    'class' => 'slug',
]);
echo $this->Form->input('description', [
    'label' => __d('croogo', 'Description'),
]);
echo $this->Form->input('types._ids', [
    'label' => __d('croogo', 'Content types'),
    'class' => 'c-select',
    'help' => __d('croogo', 'Select which content types will use this vocabulary')
]);
echo $this->Html->tabEnd();
$this->end();

$this->start('panels');
echo $this->Html->beginBox();
echo $this->element('Croogo/Core.admin/buttons', ['type' => __d('croogo', 'vocabulary')]);
echo $this->Html->endBox();

echo $this->Html->beginBox(__d('croogo', 'Options'));
echo $this->Form->input('required', [
    'label' => __d('croogo', 'Required'),
    'class' => false,
    'help' => __d('croogo', 'Required to select a term from the vocabulary.'),
]);
echo $this->Form->input('multiple', [
    'label' => __d('croogo', 'Multiple selections'),
    'class' => false,
    'help' => __d('croogo', 'Allow multiple terms to be selected.'),
]);
echo $this->Form->input('tags', [
    'label' => __d('croogo', 'Freetags'),
    'class' => false,
    'help' => __d('croogo', 'Allow free-typing of terms/tags.'),
]);
echo $this->Html->endBox();
$this->end();
