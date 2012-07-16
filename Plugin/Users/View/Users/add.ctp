<div class="users form">
	<h2><?php echo $title_for_layout; ?></h2>
	<?php echo $this->Form->create('User');?>
		<fieldset>
		<?php
			echo $this->Form->input('username');
			echo $this->Form->input('password', array('value' => ''));
			echo $this->Form->input('verify_password', array('type' => 'password', 'value' => ''));
			echo $this->Form->input('name');
			echo $this->Form->input('email');
			echo $this->Form->input('website');
		?>
		</fieldset>
	<?php echo $this->Form->end('Submit');?>
</div>