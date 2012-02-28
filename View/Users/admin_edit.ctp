<div class="users form">
<<<<<<< HEAD
	<h2><?php echo __('Edit User'); ?></h2>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('Reset password'), array('action' => 'reset_password', $this->params['pass']['0'])); ?></li>
		</ul>
	</div>

	<?php echo $this->Form->create('User');?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><a href="#user-main"><?php echo __('User'); ?></a></li>
				<?php echo $this->Layout->adminTabs(); ?>
			</ul>
=======
	<h2><?php __('Edit User'); ?></h2>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('Reset password', true), array('action' => 'reset_password', $this->params['pass']['0'])); ?></li>
		</ul>
	</div>

	<?php echo $this->Form->create('User');?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><a href="#user-main"><?php __('User'); ?></a></li>
				<?php echo $this->Layout->adminTabs(); ?>
			</ul>
>>>>>>> 1.3-whitespace

			<div id="user-main">
			<?php
				echo $this->Form->input('id');
				echo $this->Form->input('role_id');
				echo $this->Form->input('username');
				echo $this->Form->input('name');
				echo $this->Form->input('email');
				echo $this->Form->input('website');
				echo $this->Form->input('status');
			?>
			</div>
			<?php echo $this->Layout->adminTabs(); ?>
		</div>
	</fieldset>

<<<<<<< HEAD
	<div class="buttons">
	<?php
		echo $this->Form->end(__('Save'));
		echo $this->Html->link(__('Cancel'), array(
			'action' => 'index',
		), array(
			'class' => 'cancel',
		));
	?>
	</div>
=======
	<div class="buttons">
	<?php
		echo $this->Form->end(__('Save', true));
		echo $this->Html->link(__('Cancel', true), array(
			'action' => 'index',
		), array(
			'class' => 'cancel',
		));
	?>
	</div>
>>>>>>> 1.3-whitespace
</div>