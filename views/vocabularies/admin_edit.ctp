<div class="vocabularies form">
	<h2><?php echo $title_for_layout; ?></h2>

	<?php echo $this->Form->create('Vocabulary');?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><span><a href="#vocabulary-basic"><?php __('Vocabulary'); ?></a></span></li>
				<li><span><a href="#vocabulary-options"><?php __('Options'); ?></a></span></li>
				<?php echo $this->Layout->adminTabs(); ?>
			</ul>

			<div id="vocabulary-basic">
				<?php
					echo $this->Form->input('id');
					echo $this->Form->input('title');
					echo $this->Form->input('alias');
					echo $this->Form->input('description');
					echo $this->Form->input('Type.Type');
				?>
			</div>

			<div id="vocabulary-options">
				<?php
					echo $this->Form->input('required');
					echo $this->Form->input('multiple');
					echo $this->Form->input('tags');
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