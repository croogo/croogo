<?php

echo __d('croogo', 'Hello %s', $user->name) ?>,


<?php
    $url = $this->Url->build([
        'plugin' => 'Croogo/Users',
        'controller' => 'Users',
        'action' => 'reset',
        $user->username,
        $user->activation_key,
    ], true);
    echo __d('croogo', 'Please visit this link to reset your password: %s', $url);
    ?>


<?= __d('croogo', 'If you did not request a password reset, then please ignore this email.') ?>


<?= __d('croogo', 'IP Address: %s', $_SERVER['REMOTE_ADDR']) ?>
