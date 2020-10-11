<?php

$this->setLayout('admin_login');

$title = __d('croogo', 'Forgot Password');
$this->assign('title', $title);

$formStart = $this->Form->create(null, [
    'url' => [
        'controller' => 'Users',
        'action' => 'forgot',
    ],
]);

    $body = $this->Form->control('username', [
        'label' => false,
        'placeholder' => __d('croogo', 'Username/Email'),
        'prepend' => $this->Html->icon('user', ['class' => 'fa-fw']),
        'required' => true,
    ]);
    $footer = $this->Form->control(__d('croogo', 'Submit'), [
        'type' => 'submit',
        'class' => 'btn btn-primary',
    ]);
    $formEnd = $this->Form->end();

    ?>
<div class="card rounded-plus bg-faded">
    <div class="card-header">
        <h5 class="card-title"><?= $this->fetch('title') ?></h5>
    </div>
    <?= $formStart ?>
    <div class="card-body">
        <?php
        echo $this->Layout->sessionFlash();
        echo $body;
        ?>
    </div>
    <div class="card-footer text-right">
        <?= $footer ?>
    </div>
    <?= $formEnd ?>
</div>
