<h2><?php echo __('Reset password'); ?>: <?php echo $this->data['User']['username']; ?></h2>
<?php
	$this->Html
		->addCrumb($this->Html->icon('home'), '/admin')
		->addCrumb(__('Users'), array('plugin' => 'users', 'controller' => 'users', 'action' => 'index'))
		->addCrumb(__('Edit Password'), $this->here)
		->addCrumb($this->request->data['User']['name']);
?>
<?php echo $this->Form->create('User', array('url' => array('action' => 'reset_password')));?>
<div class="row-fluid">
	<div class="span8">
		<?php
			echo $this->Html->beginBox(__('Password')) .
				$this->Form->input('id') .
				$this->Form->input('password', array('label' => __('New Password'), 'value' => '')) .
				$this->Form->input('verify_password', array('label' => __('Verify Password'), 'type' => 'password', 'value' => '')) .
				$this->Html->endBox();
		?>
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
		?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
