<?php echo __('Hello %s', $user['User']['name']); ?>,

<?php
	$url = Router::url(array(
		'controller' => 'users',
		'action' => 'activate',
		$user['User']['username'],
		$user['User']['activation_key'],
	), true);
	echo __('Please visit this link to activate your account: %s', $url);
?>