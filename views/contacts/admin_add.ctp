<div class="contacts form">
	<h2><?php echo $title_for_layout; ?></h2>
	<?php echo $this->Form->create('Contact');?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><a href="#contact-basic"><?php __('Contact'); ?></a></li>
				<li><a href="#contact-details"><?php __('Details'); ?></a></li>
				<li><a href="#contact-message"><?php __('Message'); ?></a></li>
				<?php echo $this->Layout->adminTabs(); ?>
			</ul>

			<div id="contact-basic">
			<?php
				echo $this->Form->input('title');
				echo $this->Form->input('alias');
				echo $this->Form->input('email');
				echo $this->Form->input('body');
				echo $this->Form->input('status');
			?>
			</div>

			<div id="contact-details">
			<?php
				echo $this->Form->input('name');
				echo $this->Form->input('position');
				echo $this->Form->input('address');
				echo $this->Form->input('address2');
				echo $this->Form->input('state');
				echo $this->Form->input('country');
				echo $this->Form->input('postcode');
				echo $this->Form->input('phone');
				echo $this->Form->input('fax');
			?>
			</div>

			<div id="contact-message">
			<?php
				echo $this->Form->input('message_status', array(
					'label' => __('Let users leave a message', true),
				));
				echo $this->Form->input('message_archive', array(
					'label' => __('Save messages in database', true),
				));
				echo $this->Form->input('message_notify', array(
					'label' => __('Notify by email instantly', true),
				));
				echo $this->Form->input('message_spam_protection', array(
					'label' => __('Spam protection (requires Akismet API key)', true),
				));
				echo $this->Form->input('message_captcha', array(
					'label' => __('Use captcha? (requires Recaptcha API key)', true),
				));
			?>
				<p>
				<?php
					echo $this->Html->link(__('You can manage your API keys here.', true), array(
						'controller' => 'settings',
						'action' => 'prefix',
						'Service',
					));
				?>
				</p>
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