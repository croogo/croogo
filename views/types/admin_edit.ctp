<div class="types form">
	<h2><?php echo $title_for_layout; ?></h2>

	<?php echo $this->Form->create('Type');?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><a href="#type"><?php __('Type'); ?></a></li>
				<li><a href="#type-taxonomy"><?php __('Taxonomy'); ?></a></li>
				<li><a href="#type-format"><?php __('Format'); ?></a></li>
				<li><a href="#type-comments"><?php __('Comments'); ?></a></li>
				<li><a href="#type-params"><?php __('Params'); ?></a></li>
				<?php echo $this->Layout->adminTabs(); ?>
			</ul>

			<div id="type">
			<?php
				echo $this->Form->input('id');
				echo $this->Form->input('title');
				echo $this->Form->input('alias', array('disabled' => 'disabled'));
				echo $this->Form->input('description');
			?>
			</div>

			<div id="type-taxonomy">
			<?php
				echo $this->Form->input('Vocabulary.Vocabulary');
			?>
			</div>

			<div id="type-format">
			<?php
				echo $this->Form->input('format_show_author', array(
					'label' => __('Show author\'s name', true),
				));
				echo $this->Form->input('format_show_date', array(
					'label' => __('Show date', true),
				));
			?>
			</div>

			<div id="type-comments">
			<?php
				$options = array(
					'0' => __('Disabled', true),
					'1' => __('Read only', true),
					'2' => __('Read/Write', true),
				);
				echo $this->Form->input('comment_status', array(
					'type' => 'radio',
					'div' => array('class' => 'radio'),
					'options' => $options,
				));
				echo $this->Form->input('comment_approve', array(
					'label' => 'Auto approve comments',
				));
				echo $this->Form->input('comment_spam_protection', array(
					'label' => 'Spam protection (requires Akismet API key)',
				));
				echo $this->Form->input('comment_captcha', array(
					'label' => 'Use captcha? (requires Recaptcha API key)',
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

			<div id="type-params">
			<?php
				echo $this->Form->input('Type.params');
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