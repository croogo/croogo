<h2 class="hidden-desktop"><?php echo __('Reset password'); ?>: <?php echo $this->data['User']['username']; ?></h2>
<?php
	$this->Html
		->addCrumb($this->Html->icon('home'), '/admin')
		->addCrumb(__('Users'), array('plugin' => 'users', 'controller' => 'users', 'action' => 'index'))
		->addCrumb($this->request->data['User']['name'], array(
			'action' => 'edit', $this->request->data['User']['id'],
		))
		->addCrumb(__('Reset Password'), $this->here);
?>
<?php echo $this->Form->create('User', array('url' => array('action' => 'reset_password')));?>
<div class="row-fluid">
	<div class="span8">
		<ul class="nav nav-tabs">
			<li><a href="#reset-password" data-toggle="tab"><?php echo __('Reset Password'); ?></a></li>
			<?php echo $this->Croogo->adminTabs(); ?>
		</ul>

		<div class="tab-content">
			<div id="reset-password" class="tab-pane">
			<?php
				echo $this->Form->input('id');
				echo $this->Form->input('password', array('label' => __('New Password'), 'value' => ''));
				echo $this->Form->input('verify_password', array('label' => __('Verify Password'), 'type' => 'password', 'value' => ''));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>
	<div class="span4">
	<?php
		echo $this->Html->beginBox(__('Publishing')) .
			$this->Form->button(__('Reset'), array('button' => 'default')) .
			$this->Html->link(
				__('Cancel'),
				array('action' => 'index'),
				array('button' => 'primary')) .
			$this->Html->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
