<h2 class="hidden-desktop"><?php echo __d('croogo', 'Reset password'); ?>: <?php echo $this->data['User']['username']; ?></h2>
<?php
	$this->Html
		->addCrumb($this->Html->icon('home'), '/admin')
		->addCrumb(__d('croogo', 'Users'), array('plugin' => 'users', 'controller' => 'users', 'action' => 'index'))
		->addCrumb($this->request->data['User']['name'], array(
			'action' => 'edit', $this->request->data['User']['id'],
		))
		->addCrumb(__d('croogo', 'Reset Password'), '/' . $this->request->url);
?>
<?php echo $this->Form->create('User', array('url' => array('action' => 'reset_password')));?>
<div class="row-fluid">
	<div class="span8">
		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', 'Reset Password'), '#reset-password');
			echo $this->Croogo->adminTabs();
		?>
		</ul>

		<div class="tab-content">
			<div id="reset-password" class="tab-pane">
			<?php
				echo $this->Form->input('id');
				echo $this->Form->input('password', array('label' => __d('croogo', 'New Password'), 'value' => ''));
				echo $this->Form->input('verify_password', array('label' => __d('croogo', 'Verify Password'), 'type' => 'password', 'value' => ''));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>
	<div class="span4">
	<?php
		echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
			$this->Form->button(__d('croogo', 'Reset'), array('button' => 'default')) .
			$this->Html->link(
				__d('croogo', 'Cancel'),
				array('action' => 'index'),
				array('button' => 'primary')) .
			$this->Html->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
