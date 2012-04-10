<?php $this->extend('/Common/admin_edit'); ?>
<?php echo $this->Form->create('User');?>
<fieldset>
	<div class="tabs">
		<ul>
			<li><a href="#user-main"><?php echo __('User'); ?></a></li>
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
	echo $this->Form->end(__('Save'));
	echo $this->Html->link(__('Cancel'), array(
		'action' => 'index',
	), array(
		'class' => 'cancel',
	));
?>
</div>