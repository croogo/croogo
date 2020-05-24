<?php

use Cake\Routing\Router;

$this->extend('Croogo/Core./Common/admin_edit');
$this->Croogo->adminScript(['Croogo/Nodes.admin.js']);

$this->Breadcrumbs->add(__d('croogo', 'Content'), ['action' => 'index']);

if ($this->getRequest()->getParam('action') == 'add') :
    $this->assign('title', __d('croogo', 'Create content: %s', $type->title));

    $this->Breadcrumbs->add(__d('croogo', 'Create'), ['action' => 'create'])
        ->add(h($type->title), $this->getRequest()->getRequestTarget());
endif;

if ($this->getRequest()->getParam('action') == 'edit') :
    $this->assign('title', __d('croogo', 'Edit %s: %s', $node->type, $node->title));

    $this->Breadcrumbs
        ->add(h($type->title), [
            'action' => 'index',
            '?' => ['type' => $type->alias],
        ])
        ->add(h($node->title), $this->getRequest()->getRequestTarget(), [
            'innerAttrs' => [
                'title' => h($node->title),
            ],
        ]);
endif;

$this->append('form-start', $this->Form->create($node, [
    'class' => 'protected-form',
]));

$this->start('tab-heading');
    echo $this->Croogo->adminTab(__d('croogo', h($type->title)), '#node-main');
$this->end();

$this->start('tab-content');
    echo $this->Html->tabStart('node-main');
        echo $this->Form->input('id');
        echo $this->Form->input('title', [
            'label' => false,
            'placeholder' => __d('croogo', '%s title', h($type->title)),
            'data-slug' => '#slug',
            'data-slug-editable' => true,
            'data-slug-edit-class' => 'btn btn-outline-secondary',
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
            'id' => 'NodeExcerpt',
        ]);
        echo $this->Html->tabEnd();
        $this->end();

        $this->start('panels');
        $username = isset($node->user->username) ? $node->user->username : $this->getRequest()->getSession()
        ->read('Auth.User.username');
        echo $this->Html->beginBox(__d('croogo', 'Publishing'));
        echo $this->element('Croogo/Core.admin/buttons', ['type' => h($type->title)]);
        echo $this->element('Croogo/Core.admin/publishable');

        echo $this->Form->input('promote', [
        'label' => __d('croogo', 'Promoted to front page'),
        ]);
        echo $this->Html->endBox();

        echo $this->Html->beginBox(__d('croogo', '%s attributes', h($type->title)));
        echo $this->Form->autocomplete('user_id', [
            'label' => __d('croogo', 'Author'),
            'options' => $users,
            'default' => $loggedInUser['id'],
            'autocomplete' => [
                'default' => $username,
                'data-displayField' => 'username',
                'data-queryField' => 'name',
                'data-relatedElement' => '#user-id',
                'data-url' => Router::url([
                    'prefix' => 'api/v10',
                    'plugin' => 'Croogo/Users',
                    'controller' => 'Users',
                    'action' => 'lookup',
                ]),
            ],
        ]);

        echo $this->Form->autocomplete('parent_id', [
            'label' => __d('croogo', 'Parent'),
            'options' => $parents,
            'default' => $node->parent_id,
            'autocomplete' => [
                'default' => $node->parent ? h($node->parent->title) : null,
                'data-displayField' => 'title',
                'data-queryField' => 'title',
                'data-relatedElement' => '#parent-id',
                'data-url' => $this->Url->build([
                    'prefix' => 'api/v10',
                    'plugin' => 'Croogo/Nodes',
                    'controller' => 'Nodes',
                    'action' => 'lookup',
                    'type' => $node->type,
                ]),
            ],
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
        $this->end();
