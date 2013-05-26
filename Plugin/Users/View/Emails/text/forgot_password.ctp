<?php echo __d('croogo', 'Hello %s', $user['User']['name']); ?>,

<?php
	$url = Router::url(array(
		'controller' => 'users',
		'action' => 'reset',
		$user['User']['username'],
		$activationKey,
	), true);
	echo __d('croogo', 'Please visit this link to reset your password: %s', $url);
?>


<?php echo __d('croogo', 'If you did not request a password reset, then please ignore this email.'); ?>


<?php echo __d('croogo', 'IP Address: %s', $_SERVER['REMOTE_ADDR']); ?>
