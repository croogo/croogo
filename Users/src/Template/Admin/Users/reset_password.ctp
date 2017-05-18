<?php
$this->extend('Croogo/Core./Common/admin_edit');

$this->assign('title', __d('croogo', 'Reset password: %s', $user->username));
$this->Breadcrumbs
    ->add(__d('croogo', 'Users'), ['plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'index'])
    ->add($user->name, [
        'action' => 'edit',
        $user->id,
    ])
    ->add(__d('croogo', 'Reset Password'), $this->request->getRequestTarget());
$this->assign('form-start', $this->Form->create($user));

$this->start('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Reset Password'), '#reset-password');
$this->end();

$this->start('tab-content');
echo $this->Html->tabStart('reset-password');
echo $this->Form->input('password', ['label' => __d('croogo', 'New Password'), 'value' => '']);
echo $this->Form->input('verify_password',
    ['label' => __d('croogo', 'Verify Password'), 'type' => 'password', 'value' => '']);
echo $this->Html->tabEnd();
$this->end();
