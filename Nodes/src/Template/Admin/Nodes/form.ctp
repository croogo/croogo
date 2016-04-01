<?php

use Croogo\Core\Status;

$this->extend('Croogo/Core./Common/admin_edit');
$this->Html->script(array('Croogo/Nodes.admin'), ['block' => true]);

$this->Html
    ->addCrumb(__d('croogo', 'Content'), ['action' => 'index']);

if ($this->request->params['action'] == 'add') {
    $this->assign('title', __d('croogo', 'Create content: %s', $type->title));

    $formUrl = ['action' => 'add', $typeAlias];
    $this->Html->addCrumb(__d('croogo', 'Create'), ['action' => 'create'])
        ->addCrumb($type->title);
}

if ($this->request->params['action'] == 'edit') {
    $formUrl = ['action' => 'edit'];
    $this->Html->addCrumb($node->title, '/' . $this->request->url);
}

$this->append('form-start', $this->Form->create($node, [
    'url' => $formUrl,
    'class' => 'protected-form',
]));

$this->start('tab-heading');
    echo $this->Croogo->adminTab(__d('croogo', $type->title), '#node-main');
    echo $this->Croogo->adminTabs();
$this->end();

$this->start('tab-content');
    echo $this->Html->beginTabPane('node-main');
        echo $this->Form->input('id');
        echo $this->Form->input('title', [
            'label' => false,
            'placeholder' => __d('croogo', '%s title', $type->title),
        ]);
        echo $this->Form->input('slug', [
            'class' => 'slug',
            'label' => __d('croogo', 'Permalink'),
            'prepend' => $this->Url->build('/', ['fullbase' => true])
        ]);
        echo $this->Form->input('body', [
            'label' => __d('croogo', 'Body'),
            'id' => 'NodeBody',
        ]);
        echo $this->Form->input('excerpt', [
            'label' => __d('croogo', 'Excerpt'),
        ]);
    echo $this->Html->endTabPane();

    echo $this->Croogo->adminTabs();
$this->end();

$this->start('panels');
    $username = isset($node->user->username) ? $node->user->username : $this->request->session()
        ->read('Auth.User.username');
    echo $this->Html->beginBox(__d('croogo', 'Publishing'));
    echo '<div class="clearfix">';
    echo '<div class="pull-left">';
    echo $this->Form->button(__d('croogo', 'Save %s', $type->title), ['button' => 'success', 'class' => 'btn-success-outline']);
    echo '</div>';
    echo '<div class="pull-right">';
    echo $this->Html->link(__d('croogo', 'Cancel'), ['action' => 'index'], ['class' => 'cancel btn btn-danger']);
    echo '</div>';
    echo '</div>';

    echo $this->Form->input('status', [
            'label' => __d('croogo', 'Status'),
            'class' => 'c-select',
            'default' => Status::UNPUBLISHED,
            'options' => $this->Croogo->statuses(),
        ]);
    echo $this->Form->input('promote', [
            'label' => __d('croogo', 'Promoted to front page'),
            'class' => false,
        ]);

    echo $this->Html->div('input-daterange', $this->Form->input('publish_start', [
                'label' => __d('croogo', 'Publish on'),
                'empty' =>  true,
            ]) . $this->Form->input('publish_end', [
                'label' => __d('croogo', 'Un-publish on'),
                'empty' => true,
            ]));

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
    echo $this->Form->input('Role.Role', [
        'multiple' => 'checkbox'
    ]);
    echo $this->Html->endBox();

    echo $this->Croogo->adminBoxes();
$this->end();

$this->assign('form-end', $this->Form->end());
