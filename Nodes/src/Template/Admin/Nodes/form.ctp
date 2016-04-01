<?php

use Croogo\Core\Status;

$this->extend('Croogo/Core./Common/admin_edit');
$this->Html->script(array('Croogo/Nodes.admin'), ['block' => true]);

$this->Html
    ->addCrumb('', '/admin', ['icon' => 'home'])
    ->addCrumb(__d('croogo', 'Content'), ['action' => 'index']);

if ($this->request->params['action'] == 'add') {
    $this->assign('title', __d('croogo', 'Create content: %s', $type->title));

    $formUrl = ['action' => 'add', $typeAlias];
    $this->Html->addCrumb(__d('croogo', 'Create'), ['action' => 'create'])
        ->addCrumb($type->title, '/' . $this->request->url);
}

if ($this->request->params['action'] == 'edit') {
    $formUrl = ['action' => 'edit'];
    $this->Html->addCrumb($node->title, '/' . $this->request->url);
}

$lookupUrl = $this->Url->build([
    'plugin' => 'Croogo/Users',
    'controller' => 'Users',
    'action' => 'lookup',
    '_ext' => 'json',
]);

$parentTitle = isset($parentTitle) ? $parentTitle : null;
$apiUrl = $this->Url->build([
    'action' => 'lookup',
    '_ext' => 'json',
    '?' => [
        'type' => $type->alias,
    ],
]);

$this->append('form-start', $this->Form->create($node, [
    'url' => $formUrl,
    'class' => 'protected-form',
]));

$this->start('tab-heading');
    echo $this->Croogo->adminTab(__d('croogo', $type->title), '#node-main');
    echo $this->Croogo->adminTab(__d('croogo', 'Access'), '#node-access');
    echo $this->Croogo->adminTabs();
$this->end();

$this->start('tab-content');
    $this->start('node-main');
        echo $this->Form->input('id');
        echo $this->Form->input('title', [
            'label' => __d('croogo', 'Title'),
        ]);
        echo $this->Form->input('slug', [
            'class' => 'slug',
            'label' => __d('croogo', 'Slug'),
        ]);
        echo $this->Form->autocomplete('parent_id', [
            'label' => __d('croogo', 'Parent'),
            'type' => 'text',
            'autocomplete' => [
                'default' => $parentTitle,
                'data-displayField' => 'title',
                'data-primaryKey' => 'id',
                'data-queryField' => 'title',
                'data-relatedElement' => '#NodeParentId',
                'data-url' => $apiUrl,
            ],
        ]);
        echo $this->Form->input('body', [
            'label' => __d('croogo', 'Body'),
            'id' => 'NodeBody',
        ]);
        echo $this->Form->input('excerpt', [
            'label' => __d('croogo', 'Excerpt'),
        ]);
    $this->end();

    $this->start('node-access');
        echo $this->Form->input('Role.Role', ['class' => false, 'multiple' => true]);
    $this->end();

    echo $this->Croogo->adminTabPane($this->fetch('node-main'), 'node-main');
    echo $this->Croogo->adminTabPane($this->fetch('node-access'), 'node-access');
    echo $this->Croogo->adminTabs();
$this->end();

$this->start('panels');
    $username = isset($node->user->username) ? $node->user->username : $this->request->session()
        ->read('Auth.User.username');
    echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
        $this->Form->button(__d('croogo', 'Apply'), ['name' => 'apply']) .
        $this->Form->button(__d('croogo', 'Save'), ['button' => 'success']) .
        $this->Html->link(__d('croogo', 'Cancel'), ['action' => 'index'], ['class' => 'cancel btn btn-danger']) .
        $this->Form->input('status', [
            'legend' => false,
            'label' => false,
            'type' => 'radio',
            'class' => false,
            'default' => Status::UNPUBLISHED,
            'options' => $this->Croogo->statuses(),
        ]) .
        $this->Form->input('promote', [
            'label' => __d('croogo', 'Promoted to front page'),
            'class' => false,
        ]) .
        $this->Form->autocomplete('user_id', [
            'type' => 'text',
            'label' => __d('croogo', 'Publish as '),
            'class' => 'span10',
            'autocomplete' => [
                'default' => $username,
                'data-displayField' => 'username',
                'data-primaryKey' => 'id',
                'data-queryField' => 'name',
                'data-relatedElement' => '#NodeUserId',
                'data-url' => $lookupUrl,
            ],
        ]) .

        $this->Form->input('created', [
            'type' => 'text',
            'class' => 'span10 input-datetime',
        ]) .

        $this->Html->div('input-daterange', $this->Form->input('publish_start', [
                'label' => __d('croogo', 'Publish Start'),
                'type' => 'text',
            ]) . $this->Form->input('publish_end', [
                'label' => __d('croogo', 'Publish End'),
                'type' => 'text',
            ]));

    echo $this->Html->endBox();

    echo $this->Croogo->adminBoxes();
$this->end();

$this->assign('form-end', $this->Form->end());
