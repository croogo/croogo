<div class="filemanager form">
	<h2><?php echo $title_for_layout; ?></h2>

<<<<<<< HEAD
	<div class="breadcrumb">
	<?php
		echo __('You are here:') . ' ';
		$breadcrumb = $this->Filemanager->breadcrumb($path);
		foreach($breadcrumb AS $pathname => $p) {
			echo $this->Filemanager->linkDirectory($pathname, $p);
			echo DS;
		}
	?>
	</div>
=======
	<div class="breadcrumb">
	<?php
		echo __('You are here:', true) . ' ';
		$breadcrumb = $this->Filemanager->breadcrumb($path);
		foreach($breadcrumb AS $pathname => $p) {
			echo $this->Filemanager->linkDirectory($pathname, $p);
			echo DS;
		}
	?>
	</div>
>>>>>>> 1.3-whitespace

	<?php
		echo $this->Form->create('Filemanager', array(
			'url' => $this->Html->url(array(
				'controller' => 'filemanager',
				'action' => 'create_file',
			), true) . '?path=' . urlencode($path),
		));
	?>
	<fieldset>
	<?php echo $this->Form->input('Filemanager.name', array('type' => 'text')); ?>
	</fieldset>

<<<<<<< HEAD
	<div class="buttons">
	<?php
		echo $this->Form->end(__('Create'));
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
		echo $this->Form->end(__('Create', true));
		echo $this->Html->link(__('Cancel', true), array(
			'action' => 'index',
		), array(
			'class' => 'cancel',
		));
	?>
	</div>
>>>>>>> 1.3-whitespace
</div>