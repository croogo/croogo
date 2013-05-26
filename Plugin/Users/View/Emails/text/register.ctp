<?php echo __d('croogo', 'Hello %s', $user['User']['name']); ?>,

<?php
	$url = Router::url(array(
		'controller' => 'users',
		'action' => 'activate',
		$user['User']['username'],
		$user['User']['activation_key'],
	), true);
	echo __d('croogo', 'Please visit this link to activate your account: %s', $url);
?>