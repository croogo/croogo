<div class="regions form">
	<h2><?php echo $title_for_layout; ?></h2>

<<<<<<< HEAD
	<?php echo $this->Form->create('Region');?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><a href="#region-main"><span><?php echo __('Region'); ?></span></a></li>
				<?php echo $this->Layout->adminTabs(); ?>
			</ul>
=======
	<?php echo $this->Form->create('Region');?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><a href="#region-main"><span><?php __('Region'); ?></span></a></li>
				<?php echo $this->Layout->adminTabs(); ?>
			</ul>
>>>>>>> 1.3-whitespace

			<div id="region-main">
			<?php
				echo $this->Form->input('id');
				echo $this->Form->input('title');
			?>
			</div>
			<?php echo $this->Layout->adminTabs(); ?>
		</div>
	</fieldset>

<<<<<<< HEAD
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
=======
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
>>>>>>> 1.3-whitespace
</div>