<?php $this->Html->script(array('vocabularies'), false); ?>
<?php $this->extend('/Common/admin_edit'); ?>
<?php echo $this->Form->create('Vocabulary');?>
<fieldset>
	<div class="tabs">
		<ul>
			<li><span><a href="#vocabulary-basic"><?php echo __('Vocabulary'); ?></a></span></li>
			<li><span><a href="#vocabulary-options"><?php echo __('Options'); ?></a></span></li>
			<?php echo $this->Layout->adminTabs(); ?>
		</ul>

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