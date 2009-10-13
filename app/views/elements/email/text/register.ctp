<?php echo __('Hello', true) . ' ' . $user['User']['name']; ?>,

<?php
    echo __('Please visit this link to activate your account', true) . ': ';
    echo Router::url(array(
        'controller' => 'users',
        'action' => 'activate',
        $user['User']['username'],
        $user['User']['activation_key'],
    ), true);
?>