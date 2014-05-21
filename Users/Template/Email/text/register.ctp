<?php echo __d('croogo', 'Hello %s', $user['User']['name']); ?>,

<?php
if (empty($url)):
	$url = Router::url(array(
		'controller' => 'users',
		'action' => 'activate',
		$user['User']['username'],
		$user['User']['activation_key'],
	), true);
endif;
echo __d('croogo', 'Please visit this link to activate your account: %s', $url);