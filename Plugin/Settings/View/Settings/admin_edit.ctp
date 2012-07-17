<?php $this->extend('/Common/admin_edit'); ?>
<?php echo $this->Form->create('Setting');?>
<fieldset>
	<div class="tabs">
		<ul>
			<li><a href="#setting-basic"><span><?php echo __('Settings'); ?></span></a></li>
			<li><a href="#setting-misc"><span><?php echo __('Misc.'); ?></span></a></li>
		</ul>

		<div id="setting-basic">
			<?php
				echo $this->Form->input('id');
				echo $this->Form->input('key', array('rel' => __("e.g., 'Site.title'")));
				echo $this->Form->input('value');
			?>
		</div>

		<div id="setting-misc">
			<?php
				echo $this->Form->input('title');
				echo $this->Form->input('description');
				echo $this->Form->input('input_type', array('rel' => __("e.g., 'text' or 'textarea'")));
				echo $this->Form->input('editable');
				//echo $this->Form->input('weight');
				echo $this->Form->input('params');
			?>
		</div>
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