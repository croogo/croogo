<p>
<?= __d('croogo', 'Hello %s', $user->name); ?>,
</p>

<p>
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
</p>

<p>
    <?= __d('croogo', 'If you did not request a password reset, then please ignore this email.'); ?>
</p>

<p>
    <?= __d('croogo', 'IP Address: %s', $_SERVER['REMOTE_ADDR']); ?>
</p>
