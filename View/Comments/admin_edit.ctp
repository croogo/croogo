<?php $this->extend('/Common/admin_edit'); ?>
<?php echo $this->Form->create('Comment');?>
<fieldset>
	<div class="tabs">
		<ul>
			<li><a href="#comment-main"><?php echo __('Comment'); ?></a></li>
			<li><a href="#comment-contact"><?php echo __('Contact Info'); ?></a></li>
			<?php echo $this->Layout->adminTabs(); ?>
		</ul>

		<div id="comment-main">
		<?php
			echo $this->Form->input('id');
			echo $this->Form->input('title');
			echo $this->Form->input('body');
			echo $this->Form->input('status', array('label' => __('Published')));
		?>
		</div>

		<div id="comment-contact">
		<?php
			echo $this->Form->input('name');
			echo $this->Form->input('email');
			echo $this->Form->input('website');
			echo $this->Form->input('ip', array('disabled' => 'disabled'));
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