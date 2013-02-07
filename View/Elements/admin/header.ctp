<div id="header">
	<div class="container_16">
		<div class="grid_8 header-left">
		<?php
			echo $this->Html->link(__('Dashboard'),
                                array(
                                    'admin' => true,
                                    'controller' => 'settings',
                                    'action' => 'dashboard',   
                                )
                        );
			echo ' <span>|</span> ';
			echo $this->Html->link(__('Visit website'),
                                array(
                                    'admin' => false,
                                    'controller' => 'nodes',
                                    'action' => 'promoted'
                                ),
                                array(
                                    'target' => '_blank'
                                )
                        );
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