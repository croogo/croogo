<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Attachments'), array('plugin' => 'file_manager', 'controller' => 'attachments', 'action' => 'index'))
	->addCrumb($this->data['Node']['title'], $this->here);

echo $this->Form->create('Node', array('url' => array('controller' => 'attachments', 'action' => 'edit')));

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
			<li><a href="#attachment-main" data-toggle="tab"><?php echo __('Attachment'); ?></a></li>
		</ul>

		<div class="tab-content">

			<div id="attachment-main" class="tab-pane">
			<?php
				echo $this->Form->input('id');

				$fileType = explode('/', $this->data['Node']['mime_type']);
				$fileType = $fileType['0'];
				if ($fileType == 'image') {
					$imgUrl = $this->Image->resize('/uploads/'.$this->data['Node']['slug'], 200, 300, true, array('class' => 'img-polaroid'));
				} else {
					$imgUrl = $this->Html->image('/img/icons/' . $this->Filemanager->mimeTypeToImage($this->data['Node']['mime_type'])) . ' ' . $this->data['Node']['mime_type'];
				}
				echo $this->Html->link($imgUrl, $this->data['Node']['path'], array(
					'class' => 'thickbox pull-right',
				));
				$this->Form->inputDefaults(array(
					'class' => 'span6',
					'label' => false,
				));
				echo $this->Form->input('title', array(
					'placeholder' => __('Title'),
				));
				echo $this->Form->input('excerpt', array(
					'placeholder' => __('Caption'),
				));

				echo $this->Form->input('file_url', array(
					'placeholder' => __('File URL'),
					'value' => Router::url($this->data['Node']['path'], true),
					'readonly' => 'readonly')
				);

				echo $this->Form->input('file_type', array(
					'placeholder' => __('Mime Type'),
					'value' => $this->data['Node']['mime_type'],
					'readonly' => 'readonly')
				);

			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>

	<div class="span4">
	<?php
		echo $this->Html->beginBox(__('Publishing')) .
			$this->Form->button(__('Save')) .
			$this->Html->link(
				__('Cancel'),
				array('action' => 'index'),
				array('class' => 'cancel', 'button' => 'danger')
			).
			$this->Html->endBox();
	?>
	</div>
</div>
<?php echo $this->Form->end(); ?>