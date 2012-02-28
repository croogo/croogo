<div class="install">
	<h2><?php echo $title_for_layout; ?></h2>

<<<<<<< HEAD
	<?php
		echo $this->Html->link(__('Click here to build your database'), array(
			'plugin' => 'install',
			'controller' => 'install',
			'action' => 'data',
			'run' => 1,
		));
	?>
=======
	<?php
		echo $this->Html->link(__('Click here to build your database', true), array(
			'plugin' => 'install',
			'controller' => 'install',
			'action' => 'data',
			'run' => 1,
		));
	?>
>>>>>>> 1.3-whitespace
</div>