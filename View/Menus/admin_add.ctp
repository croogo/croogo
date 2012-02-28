<div class="menus form">
<<<<<<< HEAD
	<h2><?php echo $title_for_layout; ?></h2>
	<?php echo $this->Form->create('Menu');?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><a href="#menu-basic"><span><?php echo __('Menu'); ?></span></a></li>
				<li><a href="#menu-misc"><span><?php echo __('Misc.'); ?></span></a></li>
				<?php echo $this->Layout->adminTabs(); ?>
			</ul>
=======
	<h2><?php echo $title_for_layout; ?></h2>
	<?php echo $this->Form->create('Menu');?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><a href="#menu-basic"><span><?php __('Menu'); ?></span></a></li>
				<li><a href="#menu-misc"><span><?php __('Misc.'); ?></span></a></li>
				<?php echo $this->Layout->adminTabs(); ?>
			</ul>
>>>>>>> 1.3-whitespace

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