<div id="header">
	<div class="container_16">
		<div class="grid_8 header-left">
		<?php
			echo $this->Html->link(__('Dashboard'), '/admin');
			echo ' <span>|</span> ';
			echo $this->Html->link(__('Visit website'), '/');
		?>
		</div>

		<div class="grid_8 header-right">
		<?php
			echo sprintf(__("You are logged in as: %s"), $this->Session->read('Auth.User.username'));
			echo ' <span>|</span> ';
			echo $this->Html->link(__("Log out"), array('plugin' => 0, 'controller' => 'users', 'action' => 'logout'));
		?>
		</div>

		<div class="clear">&nbsp;</div>
	</div>
</div>