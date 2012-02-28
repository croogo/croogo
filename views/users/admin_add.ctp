<div class="users form">
	<h2><?php __('Add User'); ?></h2>
	<?php echo $this->Form->create('User');?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><a href="#user-main"><?php __('User'); ?></a></li>
				<?php echo $this->Layout->adminTabs(); ?>
			</ul>

			<div id="user-main">
			<?php
				echo $this->Form->input('role_id');
				echo $this->Form->input('username');
				echo $this->Form->input('password');
				echo $this->Form->input('name');
				echo $this->Form->input('email');
				echo $this->Form->input('website');
				echo $this->Form->input('status');
			?>
			</div>
			<?php echo $this->Layout->adminTabs(); ?>
		</div>
	</fieldset>

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
</div>