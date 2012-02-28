<div class="filemanager form">
	<h2><?php echo $title_for_layout; ?></h2>

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

	<?php
		echo $this->Form->create('Filemanager', array(
			'url' => $this->Html->url(array(
				'controller' => 'filemanager',
				'action' => 'editfile',
			), true) . '?path=' . urlencode($absolutefilepath),
		));
	?>
	<fieldset>
	<?php echo $this->Form->input('Filemanager.content', array('type' => 'textarea', 'value' => $content, 'class' => 'content')); ?>
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