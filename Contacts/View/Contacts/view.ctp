<div id="contact-<?php echo $contact['Contact']['id']; ?>" class="">
	<h2><?php echo $contact['Contact']['title']; ?></h2>
	<div class="contact-body">
	<?php echo $contact['Contact']['body']; ?>
	</div>

	<?php if ($contact['Contact']['message_status']): ?>
	<div class="contact-form">
	<?php
		echo $this->Form->create('Message', array(
			'url' => array(
				'plugin' => 'contacts',
				'controller' => 'contacts',
				'action' => 'view',
				$contact['Contact']['alias'],
			),
		));
		echo $this->Form->input('Message.name', array('label' => __d('croogo', 'Your name')));
		echo $this->Form->input('Message.email', array('label' => __d('croogo', 'Your email')));
		echo $this->Form->input('Message.title', array('label' => __d('croogo', 'Subject')));
		echo $this->Form->input('Message.body', array('label' => __d('croogo', 'Message')));
		if ($contact['Contact']['message_captcha']):
			echo $this->Recaptcha->display_form();
		endif;
		echo $this->Form->end(__d('croogo', 'Send'));
	?>
	</div>
	<?php endif; ?>
</div>