<?php echo __('Hello') . ' ' . $user['User']['name']; ?>,

<?php
    echo __('Please visit this link to activate your account:', true);
    echo Router::url(array(
        'controller' => 'users',
        'action' => 'activate',
        $user['User']['username'],
        $user['User']['activation_key'],
    ), true);
?>