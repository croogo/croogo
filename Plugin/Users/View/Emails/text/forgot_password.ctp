<?php echo __('Hello %s', $user['User']['name']); ?>,

<?php
	$url = Router::url(array(
		'controller' => 'users',
		'action' => 'reset',
		$user['User']['username'],
		$activationKey,
	), true);
	echo __('Please visit this link to reset your password: %s', $url);
?>


<?php echo __('If you did not request a password reset, then please ignore this email.'); ?>


<?php echo __('IP Address: %s', $_SERVER['REMOTE_ADDR']); ?>
