<div id="quick">
	<?php
		if( $this->Session->read('Auth.User.username') != null) { 
			echo __("You are logged in as ", true) . $this->Session->read('Auth.User.username'); 
			echo " | " . $this->Html->link(__("Log Out", true), array('plugin' => 0, 'controller' => 'users', 'action' => 'logout'));
		}
	?>
</div>