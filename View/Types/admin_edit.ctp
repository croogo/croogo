<?php $this->extend('/Common/admin_edit'); ?>
<?php echo $this->Form->create('Type');?>
<fieldset>
	<div class="tabs">
		<ul>
			<li><a href="#type"><?php echo __('Type'); ?></a></li>
			<li><a href="#type-taxonomy"><?php echo __('Taxonomy'); ?></a></li>
			<li><a href="#type-format"><?php echo __('Format'); ?></a></li>
			<li><a href="#type-comments"><?php echo __('Comments'); ?></a></li>
			<li><a href="#type-params"><?php echo __('Params'); ?></a></li>
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
				'label' => __('Show author\'s name'),
			));
			echo $this->Form->input('format_show_date', array(
				'label' => __('Show date'),
			));
		?>
		</div>

		<div id="type-comments">
		<?php
			$options = array(
				'0' => __('Disabled'),
				'1' => __('Read only'),
				'2' => __('Read/Write'),
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
				echo $this->Html->link(__('You can manage your API keys here.'), array(
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
	echo $this->Form->end(__('Save'));
	echo $this->Html->link(__('Cancel'), array(
		'action' => 'index',
	), array(
		'class' => 'cancel',
	));
?>
</div>