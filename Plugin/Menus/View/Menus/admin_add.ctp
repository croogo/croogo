<?php $this->extend('/Common/admin_edit'); ?>
<?php echo $this->Form->create('Menu');?>
<fieldset>
	<div class="tabs">
		<ul>
			<li><a href="#menu-basic"><span><?php echo __('Menu'); ?></span></a></li>
			<li><a href="#menu-misc"><span><?php echo __('Misc.'); ?></span></a></li>
			<?php echo $this->Layout->adminTabs(); ?>
		</ul>

		<div id="menu-basic">
			<?php
				echo $this->Form->input('title');
				echo $this->Form->input('alias');
				echo $this->Form->input('description');
				//echo $this->Form->input('status');
			?>
		</div>

		<div id="menu-misc">
			<?php
				echo $this->Form->input('params');
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