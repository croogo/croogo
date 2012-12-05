<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Contacts'), array('plugin' => 'contacts', 'controller' => 'contacts', 'action' => 'index'))
	->addCrumb(__('Messages'), array('plugin' => 'contacts', 'controller' => 'messages', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html->addCrumb($this->data['Message']['id'] . ': ' . $this->data['Message']['title'], $this->here);
}

echo $this->Form->create('Message');

?>
<div class="row-fluid">
	<div class="span8">
		<ul class="nav nav-tabs">
			<li><a href="#message-main" data-toggle="tab"><?php echo __('Message'); ?></a>
		</ul>

		<div class="tab-content">
			<div id="message-main">
			<?php
				echo $this->Form->input('id');
				$this->Form->inputDefaults(array(
					'label' => false,
					'class' => 'span10',
				));
				echo $this->Form->input('name', array(
					'placeholder' => __('Name'),
				));
				echo $this->Form->input('email', array(
					'placeholder' => __('Email'),
				));
				echo $this->Form->input('title', array(
					'placeholder' => __('Title'),
				));
				echo $this->Form->input('body', array(
					'placeholder' => __('Body'),
				));
				echo $this->Form->input('phone', array(
					'placeholder' => __('Phone'),
				));
				echo $this->Form->input('address', array(
					'placeholder' => __('Address'),
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
				__('Cancel'),
				array('action' => 'index'),
				array('button' => 'danger')
			) .
			$this->Html->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->Form->end(); ?>