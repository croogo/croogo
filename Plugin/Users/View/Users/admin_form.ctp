<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb($this->Html->icon('home'), '/admin')
	->addCrumb(__('Users'), array('plugin' => 'users', 'controller' => 'users', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html->addCrumb($this->data['User']['name'], array(
		'plugin' => 'users', 'controller' => 'users', 'action' => 'edit',
		$this->data['User']['id']
	));
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html->addCrumb(__('Add'), array('plugin' => 'users','controller' => 'users', 'action' => 'add'));
}
?>
<?php $this->start('actions'); ?>
<?php if ($this->request->params['action'] == 'admin_edit'): ?>
<?php
	echo $this->Croogo->adminAction(__('Reset password'), array('action' => 'reset_password', $this->params['pass']['0']));
?>
<?php endif; ?>
<?php $this->end(); ?>

<?php echo $this->Form->create('User');?>

<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__('User'), '#user-main');
			echo $this->Croogo->adminTabs();
		?>
		</ul>

		<div class="tab-content">

			<div id="user-main" class="tab-pane">
			<?php
				echo $this->Form->input('id');
				echo $this->Form->input('role_id', array('label' => __('Role')));
				$this->Form->inputDefaults(array(
					'class' => 'span10',
					'label' => false,
				));
				echo $this->Form->input('username', array(
					'label' => __('Username'),
				));
				echo $this->Form->input('name', array(
					'label' => __('Name'),
				));
				echo $this->Form->input('email', array(
					'label' => __('Email'),
				));
				echo $this->Form->input('website', array(
					'label' => __('Website'),
				));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>

	<div class="span4">
	<?php
		echo $this->Html->beginBox(__('Publishing')) .
			$this->Form->button(__('Save'), array('button' => 'default')) .
			$this->Html->link(
			__('Cancel'), array('action' => 'index'),
			array('button' => 'danger')) .

			$this->Form->input('status', array(
				'label' => __('Status'),
				'class' => false,
			)) .

			$this->Html->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>

</div>
<?php echo $this->Form->end(); ?>