<?php

$this->extend('Croogo/Core./Common/admin_edit');
$this->Html->script(array('Croogo/Nodes.admin'), ['block' => true]);

$this->Html
    ->addCrumb(__d('croogo', 'Content'), ['action' => 'index']);

if ($this->request->params['action'] == 'add') {
    $this->assign('title', __d('croogo', 'Create content: %s', $type->title));

    $this->Html->addCrumb(__d('croogo', 'Create'), ['action' => 'create'])
        ->addCrumb($type->title);
}

if ($this->request->params['action'] == 'edit') {
    $this->Html->addCrumb($node->title);
}

$this->append('form-start', $this->Form->create($node, [
    'class' => 'protected-form',
]));

$this->start('tab-heading');
    echo $this->Croogo->adminTab(__d('croogo', $type->title), '#node-main');
$this->end();

$this->start('tab-content');
    echo $this->Html->tabStart('node-main');
        echo $this->Form->input('title', [
            'label' => false,
            'placeholder' => __d('croogo', '%s title', $type->title),
            'data-slug' => '#slug',
            'data-slug-editable' => true,
            'data-slug-edit-class' => 'btn btn-secondary btn-sm',
        ]);
        echo $this->Form->input('slug', [
            'class' => 'slug',
            'label' => __d('croogo', 'Permalink'),
            'prepend' => str_replace('_placeholder', '', $this->Url->build([
                'prefix' => false,
                'action' => 'view',
                'type' => $type->alias,
                'slug' => '_placeholder'
            ], ['fullbase' => true]))
        ]);
        echo $this->Form->input('body', [
            'label' => __d('croogo', 'Body'),
            'id' => 'NodeBody',
            'class' => !$type->format_use_wysiwyg ? 'no-wysiwyg' : ''
        ]);
        echo $this->Form->input('excerpt', [
            'label' => __d('croogo', 'Excerpt'),
        ]);
    echo $this->Html->tabEnd();
$this->end();

$this->start('panels');
    $username = isset($node->user->username) ? $node->user->username : $this->request->session()
        ->read('Auth.User.username');
    echo $this->Html->beginBox(__d('croogo', 'Publishing'));
    echo $this->element('Croogo/Core.admin/buttons', ['type' => $type->title]);
    echo $this->element('Croogo/Core.admin/publishable');

    echo $this->Form->input('promote', [
        'label' => __d('croogo', 'Promoted to front page'),
        'class' => false,
    ]);
    echo $this->Html->endBox();

    echo $this->Html->beginBox(__d('croogo', '%s attributes', $type->title));
        echo $this->Form->input('user_id', [
            'label' => __d('croogo', 'Author'),
            'options' => $users,
            'class' => 'c-select',
        ]);
        echo $this->Form->input('parent_id', [
            'label' => __d('croogo', 'Parent'),
            'options' => $parents,
            'empty' => '(no parent)',
            'class' => 'c-select',
        ]);
    echo $this->Html->endBox();

    echo $this->Html->beginBox(__d('croogo', 'Access control'));
    echo $this->Form->input('roles', [
        'multiple' => 'checkbox'
    ]);
    echo $this->Html->endBox();
$this->end();
