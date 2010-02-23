<?php echo sprintf(__('Hello %s', true), $user['User']['name']); ?>,

<?php
    $url = Router::url(array(
        'controller' => 'users',
        'action' => 'reset',
        $user['User']['username'],
        $activationKey,
    ), true);
    echo sprintf(__('Please visit this link to reset your password: %s', true), $url);
?>


<?php echo __('If you did not request a password reset, then please ignore this email.', true); ?>


<?php echo sprintf(__('IP Address: %s', true), $_SERVER['REMOTE_ADDR']); ?>