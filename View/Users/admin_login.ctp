<div class="users form">
<<<<<<< HEAD
	<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'login')));?>
		<fieldset>
		<?php
			echo $this->Form->input('username');
			echo $this->Form->input('password');
		?>
		</fieldset>
	<?php
		echo $this->Html->link(__('Forgot password?'), array(
			'admin' => false,
			'controller' => 'users',
			'action' => 'forgot',
		), array(
			'class' => 'forgot',
		));
		echo $this->Form->end(__('Log In'));
	?>
=======
	<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'login')));?>
		<fieldset>
		<?php
			echo $this->Form->input('username');
			echo $this->Form->input('password');
		?>
		</fieldset>
	<?php
		echo $this->Html->link(__('Forgot password?', true), array(
			'admin' => false,
			'controller' => 'users',
			'action' => 'forgot',
		), array(
			'class' => 'forgot',
		));
		echo $this->Form->end(__('Log In', true));
	?>
>>>>>>> 1.3-whitespace
</div>