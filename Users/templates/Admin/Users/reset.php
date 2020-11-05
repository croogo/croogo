<?php
/**
 * @var \App\View\AppView $this
 * @var \Croogo\Users\Model\Entity\User $user
 */

$this->setLayout('admin_login');

echo $this->Form->create($user);

    echo $this->Form->control('password', [
        'type' => 'password',
        'placeholder' => __d('croogo', 'New password'),
        'label' => false,
        'value' => '',
    ]);

    echo $this->Form->control('verify_password', [
        'type' => 'password',
        'placeholder' => __d('croogo', 'Verify Password'),
        'label' => false,
        'value' => '',
    ]);

    echo $this->Form->control(__d('croogo', 'Reset'), [
        'type' => 'submit',
        'class' => 'btn btn-primary',
        'templates' => [
            'submitContainer' => '<div class="float-right">{{content}}</div>',
        ],
    ]);

    echo $this->Form->end();
