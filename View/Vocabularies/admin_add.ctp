<?php
	$this->Html->script(array('vocabularies'), false);
?>
<div class="vocabularies form">
<<<<<<< HEAD
	<h2><?php echo $title_for_layout; ?></h2>
	<?php echo $this->Form->create('Vocabulary');?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><span><a href="#vocabulary-basic"><?php echo __('Vocabulary'); ?></a></span></li>
				<li><span><a href="#vocabulary-options"><?php echo __('Options'); ?></a></span></li>
				<?php echo $this->Layout->adminTabs(); ?>
			</ul>
=======
	<h2><?php echo $title_for_layout; ?></h2>
	<?php echo $this->Form->create('Vocabulary');?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><span><a href="#vocabulary-basic"><?php __('Vocabulary'); ?></a></span></li>
				<li><span><a href="#vocabulary-options"><?php __('Options'); ?></a></span></li>
				<?php echo $this->Layout->adminTabs(); ?>
			</ul>
>>>>>>> 1.3-whitespace

			<div id="vocabulary-basic">
				<?php
					echo $this->Form->input('title');
					echo $this->Form->input('alias', array('class' => 'alias'));
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