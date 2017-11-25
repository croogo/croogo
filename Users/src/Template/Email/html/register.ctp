<?php

use Cake\Routing\Router;

?>
<p>
<?= __d('croogo', 'Hello %s', $user->name); ?>,
</p>

<p>
<?= __d('croogo', 'Please visit this link to activate your account: %s', Router::url(['prefix' => false, 'plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'activate', $user->username, $user->activation_key], true));
?>
</p>
