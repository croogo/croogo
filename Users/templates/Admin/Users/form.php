<?php
/**
 * @var \App\View\AppView $this
 * @var \Croogo\Users\Model\Entity\User $user
 */
$this->Html->script('Croogo/Users.admin', ['block' => true]);

use Cake\I18n\Time;

$this->extend('Croogo/Core./Common/admin_edit');

$this->Breadcrumbs->add(
    __d('croogo', 'Users'),
    ['plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'index']
);

if ($this->getRequest()->getParam('action') == 'edit') {
    $this->Breadcrumbs->add(h($user->name), $this->getRequest()->getRequestTarget());
    $this->assign('title', __d('croogo', 'Edit user %s', $user->username));
} else {
    $this->assign('title', __d('croogo', 'New user'));
    $this->Breadcrumbs->add(__d('croogo', 'New user'), $this->getRequest()->getRequestTarget());
}

$this->start('action-buttons');
if ($this->getRequest()->getParam('action') == 'edit') :
    echo $this->Croogo->adminAction(__d('croogo', 'Reset password'), ['action' => 'resetPassword', $user->id]);
    echo $this->Croogo->adminAction(__d('croogo', 'Reset token'), ['action' => 'resetToken', $user->id]);
endif;
$this->end();

$this->append('form-start', $this->Form->create($user, [
    'fieldAccess' => [
        'User.role_id' => 1,
    ],
    'class' => 'protected-form',
]));

$this->append('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'User'), '#user-main');
$this->end();

$this->append('tab-content');

echo $this->Html->tabStart('user-main');
echo $this->Form->control('username', [
    'label' => __d('croogo', 'Username'),
]);
echo $this->Form->control('name', [
    'label' => __d('croogo', 'Name'),
]);
echo $this->Form->control('email', [
    'label' => __d('croogo', 'Email'),
]);
echo $this->Form->control('website', [
    'label' => __d('croogo', 'Website'),
]);
echo $this->Form->control('timezone', [
    'type' => 'select',
    'required' => true,
    'empty' => true,
    'options' => Time::listTimezones(),
    'label' => __d('croogo', 'Timezone'),
    'class' => 'c-select',
]);
echo $this->Form->control('role_id', [
    'label' => __d('croogo', 'Role'),
    'class' => 'c-select',
    'required' => true,
    'empty' => true,
]);
echo $this->Html->tabEnd();
$this->end();

$this->append('panels');
echo $this->Html->beginBox(__d('croogo', 'Publishing'));
echo $this->element('Croogo/Core.admin/buttons', ['type' => 'user']);

if ($this->getRequest()->getParam('action') == 'add') :
    echo $this->Form->control('notification', [
        'label' => __d('croogo', 'Send Activation Email'),
        'type' => 'checkbox',
        'class' => false,
    ]);
endif;

echo $this->Form->control('status', [
    'label' => __d('croogo', 'Active'),
]);

$showPassword = !empty($user->status);
if ($this->getRequest()->getParam('action') == 'add') :
    $out = $this->Form->control('password', [
        'label' => __d('croogo', 'Password'),
        'disabled' => !$showPassword,
    ]);
    $out .= $this->Form->control('verify_password', [
        'label' => __d('croogo', 'Verify Password'),
        'disabled' => !$showPassword,
        'type' => 'password',
    ]);

    $this->Form->unlockField('password');
    $this->Form->unlockField('verify_password');

    echo $this->Html->div(null, $out, [
        'id' => 'passwords',
        'style' => $showPassword ? '' : 'display: none',
    ]);
endif;

echo $this->Html->endBox();

echo $this->Croogo->adminBoxes();
$this->end();
