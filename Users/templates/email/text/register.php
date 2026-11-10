<?php use Cake\Routing\Router;

echo __d('croogo', 'Hello %s', $user->name); ?>,

<?php
echo __d('croogo', 'Please visit this link to activate your account: %s', Router::url(['prefix' => false, 'plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'activate', $user->username, $user->activation_key], true));
