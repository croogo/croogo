<?php
$this->extend('Croogo/Core./Common/admin_edit');
$this->Croogo->adminScript(['Croogo/Users.admin.js']);

$this->assign('title', __d('croogo', 'Reset token: %s', $user->username));
$this->Breadcrumbs
    ->add(__d('croogo', 'Users'), ['plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'index'])
    ->add($user->name, [
        'action' => 'edit',
        $user->id,
    ])
    ->add(__d('croogo', 'Reset Token'), $this->getRequest()->getRequestTarget());
$this->assign('form-start', $this->Form->create($user));

$this->start('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Reset Token'), '#reset-token');
$this->end();

$this->start('tab-content');
echo $this->Html->tabStart('reset-token');
echo $this->Form->control('token', ['label' => __d('croogo', 'New Token'), 'value' => '',
    'append' => $this->Form->button($this->Html->icon('refresh'), [
        'type' => 'button',
        'escapeTitle' => false,
        'onclick' => 'document.getElementById("token").value = Users.generateToken();',
    ]),
]);
echo $this->Html->tabEnd();

$this->end();
