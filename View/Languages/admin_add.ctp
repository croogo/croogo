<?php $this->extend('/Common/admin_edit'); ?>
<?php echo $this->Form->create('Language'); ?>
<fieldset>
	<div class="tabs">
		<ul>
			<li><a href="#language-basic"><?php echo __('Language'); ?></a></li>
			<?php echo $this->Layout->adminTabs(); ?>
		</ul>

		<div id="language-basic">
		<?php
			echo $this->Form->input('title');
			echo $this->Form->input('native');
			echo $this->Form->input('alias');
			echo $this->Form->input('status');
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