<div class="filemanager form">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="breadcrumb">
	<?php
		echo __('You are here:') . ' ';
		$breadcrumb = $this->FileManager->breadcrumb($path);
		foreach ($breadcrumb as $pathname => $p) {
			echo $this->FileManager->linkDirectory($pathname, $p);
			echo DS;
		}
	?>
	</div>

	<?php
		echo $this->Form->create('FileManager', array(
			'url' => $this->Html->url(array(
				'controller' => 'file_manager',
				'action' => 'editfile',
			), true) . '?path=' . urlencode($absolutefilepath),
		));
	?>
	<fieldset>
	<?php echo $this->Form->input('FileManager.content', array('type' => 'textarea', 'value' => $content, 'class' => 'content')); ?>
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
</div>