<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Contacts'), array('controller' => 'contacts', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html->addCrumb($this->request->data['Contact']['title']);
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html->addCrumb(__('Add'), $this->here);
}

echo $this->Form->create('Contact');

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
			<li><a href="#contact-basic" data-toggle="tab"><?php echo __('Contact'); ?></a></li>
			<li><a href="#contact-details" data-toggle="tab"><?php echo __('Details'); ?></a></li>
			<li><a href="#contact-message" data-toggle="tab"><?php echo __('Message'); ?></a></li>
			<?php echo $this->Croogo->adminTabs(); ?>
		</ul>

		<div class="tab-content">

			<div id="contact-basic" class="tab-pane">
			<?php
				echo $this->Form->input('id');
				$this->Form->inputDefaults(array('class' => 'span10'));
				echo $this->Form->input('title', array(
					'label' => false,
					'placeholder' => __('Title'),
				));
				echo $this->Form->input('alias', array(
					'label' => false,
					'placeholder' => __('Alias'),
				));
				echo $this->Form->input('email', array(
					'label' => false,
					'placeholder' => __('Email')
				));
				echo $this->Form->input('body', array(
					'label' => false,
					'placeholder' => __('Body'),
				));
			?>
			</div>

			<div id="contact-details" class="tab-pane">
			<?php
				echo $this->Form->input('name', array(
					'label' => false,
					'placeholder' => __('Name'),
				));
				echo $this->Form->input('position', array(
					'label' => false,
					'placeholder' => __('Position'),
				));
				echo $this->Form->input('address', array(
					'label' => false,
					'placeholder' => __('Address'),
				));
				echo $this->Form->input('address2', array(
					'label' => false,
					'placeholder' => __('Address2'),
				));
				echo $this->Form->input('state', array(
					'label' => false,
					'placeholder' => __('State'),
				));
				echo $this->Form->input('country', array(
					'label' => false,
					'placeholder' => __('Country'),
				));
				echo $this->Form->input('postcode', array(
					'label' => false,
					'placeholder' => __('Post Code'),
				));
				echo $this->Form->input('phone', array(
					'label' => false,
					'placeholder' => __('Phone'),
				));
				echo $this->Form->input('fax', array(
					'label' => false,
					'placeholder' => __('Fax'),
				));
			?>
			</div>

			<div id="contact-message" class="tab-pane">
			<?php
				echo $this->Form->input('message_status', array(
					'label' => __('Let users leave a message'),
					'class' => false,
				));
				echo $this->Form->input('message_archive', array(
					'label' => __('Save messages in database'),
					'class' => false,
				));
				echo $this->Form->input('message_notify', array(
					'label' => __('Notify by email instantly'),
					'class' => false,
				));
				echo $this->Form->input('message_spam_protection', array(
					'label' => __('Spam protection (requires Akismet API key)'),
					'class' => false,
				));
				echo $this->Form->input('message_captcha', array(
					'label' => __('Use captcha? (requires Recaptcha API key)'),
					'class' => false,
				));

				echo $this->Html->link(__('You can manage your API keys here.'), array(
					'plugin' => 'settings',
					'controller' => 'settings',
					'action' => 'prefix',
					'Service',
				));

				echo $this->Croogo->adminTabs();
			?>
			</div>
		</div>
	</div>

	<div class="span4">
	<?php
		echo $this->Html->beginBox(__('Publishing')) .
			$this->Form->button(__('Apply'), array('name' => 'apply')) .
			$this->Form->button(__('Save')) .
			$this->Html->link(
			__('Cancel'),
			array('action' => 'index'),
			array('button' => 'danger')
		) .
			$this->Form->input('status', array('label' => __('Published'), 'class' => false)) .
		$this->Html->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>

<?php echo $this->Form->end(); ?>
