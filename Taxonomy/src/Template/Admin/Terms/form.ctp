<?php
$this->extend('Croogo/Core./Common/admin_edit');

$this->Croogo->adminScript('Croogo/Taxonomy.terms');

$this->Breadcrumbs
    ->add(__d('croogo', 'Content'), [
        'plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'index',
    ])
    ->add(__d('croogo', 'Vocabularies'), [
        'controller' => 'Vocabularies', 'action' => 'index',
    ]);

if (isset($vocabulary)) :
    $this->Breadcrumbs->add($vocabulary->title, [
        'controller' => 'Taxonomies', 'action' => 'index', 'vocabulary_id' => $vocabulary->id,
    ]);
else :
    $this->Breadcrumbs->add(__d('croogo', 'Terms'), [
        'controller' => 'Terms', 'action' => 'index',
    ]);
endif;

$termId = isset($this->getRequest()->getParam('pass')[0]) ? $this->getRequest()->getParam('pass')[0] : null;
$action = $this->getRequest()->getParam('action');
if ($action === 'edit') :
    if (isset($vocabulary)) :
        $this->assign('title', __d('croogo', '%s: Edit Term', $vocabulary->title));
    else :
        $this->assign('title', __d('croogo', 'Edit Term: %s', $term->title));
    endif;
    $this->Breadcrumbs->add($term->title, $this->getRequest()->getRequestTarget());
endif;

if ($action === 'add') :
    $this->assign('title', __d('croogo', '%s: Add Term', $vocabulary->title));
    $this->Breadcrumbs->add(__d('croogo', 'Add'), $this->getRequest()->getRequestTarget());
endif;

if (isset($vocabularyId)) :
    $cancelUrl = [
        'controller' => 'Taxonomies',
        'action' => 'index',
        'vocabulary_id' => $vocabularyId,
    ];
else :
    $cancelUrl = [
        'controller' => 'Terms',
        'action' => 'index',
    ];
endif;
$this->set('cancelUrl', $cancelUrl);

$formUrl = [
    'action' => $this->getRequest()->getParam('action'),
    $termId,
    'vocabulary_id' => $vocabulary->id,
];

$this->assign('form-start', $this->Form->create($term, [
    'url' => $formUrl,
]));

$this->append('tab-heading');
    echo $this->Croogo->adminTab(__d('croogo', 'Term'), '#term-basic');
    echo $this->Croogo->adminTab(__d('croogo', 'Misc.'), '#term-misc');
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

        if ($action === 'add') :
            echo $this->Form->input('taxonomies.0.vocabulary_id', [
                'type' => 'hidden',
                'value' => $vocabularyId,
            ]);
        endif;

        if ($action === 'edit') :
            if (isset($vocabularyId)) :
                echo $this->Form->input('taxonomies.0.id', ['type' => 'hidden']);
                echo $this->Form->input('taxonomies.0.term_id', ['type' => 'hidden']);
                echo $this->Form->input('taxonomies.0.vocabulary_id', ['type' => 'hidden']);
                echo $this->Form->input('taxonomies.0.parent_id', [
                    'options' => $parentTree,
                    'empty' => '(no parent)',
                    'label' => __d('croogo', 'Parent'),
                    'class' => 'c-select',
                ]);
            else :
                echo $this->Form->input('taxonomies._ids', [
                    'type' => 'select',
                    'multiple' => true,
                    'value' => array_keys($taxonomies),
                    'empty' => true,
                ]);
            endif;
        endif;

        echo $this->Form->input('description', [
            'label' => __d('croogo', 'Description'),
        ]);

        echo $this->Html->tabEnd();

        echo $this->Html->tabStart('term-misc');
        echo $this->Form->input('params', [
            'label' => __d('croogo', 'Params'),
            'type' => 'stringlist',
        ]);
        echo $this->Html->tabEnd();

        $this->end();

        $this->start('buttons');
        echo $this->Html->beginBox(__d('croogo', 'Publishing'));
        echo $this->element('Croogo/Core.admin/buttons', ['type' => 'Terms']);
        echo $this->Html->endBox();
        $this->end();
