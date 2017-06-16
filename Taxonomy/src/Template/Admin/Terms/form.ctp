<?php
$this->extend('Croogo/Core./Common/admin_edit');

$this->Croogo->adminScript('Croogo/Taxonomy.terms');

$this->Breadcrumbs->add(__d('croogo', 'Content'),
    ['plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'index']);

if ($this->request->param('action') === 'edit'):
    $this->Breadcrumbs->add(__d('croogo', 'Vocabularies'), ['controller' => 'Vocabularies', 'action' => 'index'])
        ->add($vocabulary->title, ['action' => 'index', 'vocabulary_id' => $vocabulary->id])
        ->add($term->title, $this->request->getRequestTarget());
endif;

if ($this->request->param('action') === 'add'):
    $this->assign('title', __d('croogo', '%s: Add Term', $vocabulary->title));

    $this->Breadcrumbs->add(__d('croogo', 'Vocabularies'),
        ['controller' => 'Vocabularies', 'action' => 'index', $vocabulary->id])
        ->add($vocabulary->title, ['action' => 'index', 'vocabulary_id' => $vocabulary->id])
        ->add(__d('croogo', 'Add'), $this->request->getRequestTarget());
endif;

$this->set('cancelUrl', ['action' => 'index', $vocabularyId]);

$formUrl = [
    'action' => $this->request->param('action'),
    isset($this->request->pass[0]) ? $this->request->pass[0] : null,
    'vocabulary_id' => $vocabulary->id,
];

$this->assign('form-start', $this->Form->create($term, [
    'url' => $formUrl,
]));

$this->append('tab-heading');
    echo $this->Croogo->adminTab(__d('croogo', 'Term'), '#term-basic');
$this->end();

$this->append('tab-content');
    echo $this->Html->tabStart('term-basic');
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
        echo $this->Form->input('taxonomies.0.id');
        echo $this->Form->hidden('taxonomies.0.vocabulary_id', [
            'value' => $vocabulary->id,
        ]);
        echo $this->Form->input('description', [
            'label' => __d('croogo', 'Description'),
        ]);

    echo $this->Html->tabEnd();
$this->end();

$this->start('buttons');
    echo $this->Html->beginBox(__d('croogo', 'Publishing'));
    echo $this->element('Croogo/Core.admin/buttons', ['type' => 'Terms']);
    echo $this->Html->endBox();
$this->end();
