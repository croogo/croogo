<div class="messages form">
	<h2><?php echo $title_for_layout; ?></h2>

	<?php echo $this->Form->create('Message');?>
	<fieldset>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('email');
		echo $this->Form->input('title');
		echo $this->Form->input('body');
		echo $this->Form->input('phone');
		echo $this->Form->input('address');
	?>
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