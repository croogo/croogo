<div class="filemanager form">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="breadcrumb">
	<?php echo __('You are here:');

		$breadcrumb = $this->FileManager->breadcrumb($path);
		foreach ($breadcrumb as $pathname => $p) {
			echo $this->FileManager->linkDirectory($pathname, $p);
			echo DS;
		}
	?>
	</div>

	<?php
		echo $this->Form->create('FileManager', array(
			'type' => 'file',
			'url' => $this->Html->url(array(
				'controller' => 'file_manager',
				'action' => 'upload',
			), true) . '?path=' . urlencode($path),
		));
	?>
	<fieldset>
	<?php echo $this->Form->input('FileManager.file', array('type' => 'file')); ?>
	</fieldset>

	<div class="buttons">
	<?php
		echo $this->Form->end(__('Upload'));
		echo $this->Html->link(__('Cancel'), array(
			'action' => 'index',
		), array(
			'class' => 'cancel',
		));
	?>
	</div>
</div>