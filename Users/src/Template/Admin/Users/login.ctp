<?php
use Cake\Core\Configure;

$this->assign('title', __d('croogo', 'Login'));

echo $this->Form->create(false, ['url' => ['action' => 'login']]);
$this->Form->templates([
    'label' => false,
    'textContainer' => '<div class="input-prepend {{type}}"><span class="add-on"><i class="icon-user"></i></span>{{content}}</div>',
    'passwordContainer' => '<div class="input-prepend {{type}}"><span class="add-on"><i class="icon-key"></i></span>{{content}}</div>',
]);
echo $this->Form->input('username', [
    'placeholder' => __d('croogo', 'Username'),
    'label' => false,
    'prepend' => $this->Html->icon('user', ['class' => 'fa-fw']),
]);
echo $this->Form->input('password', [
    'placeholder' => __d('croogo', 'Password'),
    'label' => false,
    'prepend' => $this->Html->icon('key', ['class' => 'fa-fw']),
]);
if (Configure::read('Access Control.autoLoginDuration')):
    echo $this->Form->input('remember', [
        'label' => __d('croogo', 'Remember me?'),
        'type' => 'checkbox',
        'default' => false,
    ]);
endif;
echo $this->Form->button(__d('croogo', 'Log In'), ['class' => 'btn btn-primary']);
echo $this->Html->link(__d('croogo', 'Forgot password?'), [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Users',
    'controller' => 'Users',
    'action' => 'forgot',
], [
    'class' => 'forgot',
]);
echo $this->Form->end();
