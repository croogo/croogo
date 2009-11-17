<?php echo __('Hello', true) . ' ' . $user['User']['name']; ?>,

<?php
    echo __('Please visit this link to reset your password', true) . ': ';
    echo Router::url(array(
        'controller' => 'users',
        'action' => 'reset',
        $user['User']['username'],
        $activationKey,
    ), true);
?>


<?php echo __('If you did not request a password reset, then please ignore this email.', true); ?>


<?php echo __('IP Address: ', true) . $_SERVER['REMOTE_ADDR']; ?>