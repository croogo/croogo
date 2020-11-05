<?php
/**
 * @var \App\View\AppView $this
 * @var mixed $user
 */
$this->assign('before', $this->Form->create($user, [
    'align' => ['left' => 4, 'middle' => 8, 'right' => 0],
]));
?>
<?php
echo $this->Form->control('username', [
    'placeholder' => __d('croogo', 'Username'),
    'prepend' => $this->Html->icon('user'),
    'label' => __d('croogo', 'Username'),
    'value' => '',
]);
echo $this->Form->control('password', [
    'placeholder' => __d('croogo', 'New Password'),
    'value' => '',
    'prepend' => $this->Html->icon('key'),
    'label' => __d('croogo', 'New Password'),
]);
echo $this->Form->control('verify_password', [
    'placeholder' => __d('croogo', 'Verify Password'),
    'type' => 'password',
    'value' => '',
    'prepend' => $this->Html->icon('key'),
    'label' => __d('croogo', 'Verify Password'),
]);
?>
<?php
$this->assign('buttons', $this->Form->button(__d('croogo', 'Finalize installation'), ['class' => 'success']));
$this->assign('after', $this->Form->end());
