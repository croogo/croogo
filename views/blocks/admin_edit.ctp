<div class="blocks form">
	<h2><?php __('Edit Block'); ?></h2>
	<?php echo $this->Form->create('Block');?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><a href="#block-basic"><span><?php __('Block'); ?></span></a></li>
				<li><a href="#block-access"><span><?php __('Access'); ?></span></a></li>
				<li><a href="#block-visibilities"><span><?php __('Visibilities'); ?></span></a></li>
				<li><a href="#block-params"><?php __('Params'); ?></a></li>
				<?php echo $this->Layout->adminTabs(); ?>
			</ul>

			<div id="block-basic">
				<?php
					echo $this->Form->input('id');
					echo $this->Form->input('title');
					echo $this->Form->input('show_title');
					echo $this->Form->input('alias', array('rel' => __('unique name for your block', true)));
					echo $this->Form->input('region_id', array('rel' => __('if you are not sure, choose \'none\'', true)));
					echo $this->Form->input('body');
					echo $this->Form->input('class');
					echo $this->Form->input('element');
					echo $this->Form->input('status');
				?>
			</div>

			<div id="block-access">
				<?php
					echo $this->Form->input('Role.Role');
				?>
			</div>

			<div id="block-visibilities">
				<?php
					echo $this->Form->input('Block.visibility_paths', array(
						'class' => 'wide',
						'rel' => __('Enter one URL per line. Leave blank if you want this Block to appear in all pages.', true)
					));
					/*echo $this->Form->input('Block.visibility_php', array(
						'class' => 'wide',
						'rel' => __('Block will be shown if the PHP code returns true. If unsure, leave blank.', true)
					));*/
				?>
			</div>

			<div id="block-params">
			<?php
				echo $this->Form->input('Block.params');
			?>
			</div>
			<?php echo $this->Layout->adminTabs(); ?>
		</div>
	</fieldset>

	<div class="buttons">
	<?php
		echo $this->Form->submit(__('Apply', true), array('name' => 'apply'));
		echo $this->Form->end(__('Save', true));
		echo $this->Html->link(__('Cancel', true), array(
			'action' => 'index',
		), array(
			'class' => 'cancel',
		));
	?>
	</div>
</div>