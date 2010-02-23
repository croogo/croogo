<div id="quick">
	<?php
		if( $session->read('Auth.User.username') != null) { 
			echo __("You are logged in as ", true) . $session->read('Auth.User.username'); 
			echo " | " . $html->link(__("Log Out", true), array('plugin' => 0, 'controller' => 'users', 'action' => 'logout'));
		}
	?>
</div>