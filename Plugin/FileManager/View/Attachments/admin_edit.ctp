<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Attachments'), array('plugin' => 'file_manager', 'controller' => 'attachments', 'action' => 'index'))
	->addCrumb($this->data['Attachment']['title'], '/' . $this->request->url);

echo $this->Form->create('Attachment', array('url' => array('controller' => 'attachments', 'action' => 'edit')));

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', 'Attachment'), '#attachment-main');
		?>
		</ul>

		<div class="tab-content">

			<div id="attachment-main" class="tab-pane">
			<?php
				echo $this->Form->input('id');

				$fileType = explode('/', $this->data['Attachment']['mime_type']);
				$fileType = $fileType['0'];
				if ($fileType == 'image') {
					$imgUrl = $this->Image->resize('/uploads/' . $this->data['Attachment']['slug'], 200, 300, true, array('class' => 'img-polaroid'));
				} else {
					$imgUrl = $this->Html->image('/croogo/img/icons/' . $this->Filemanager->mimeTypeToImage($this->data['Attachment']['mime_type'])) . ' ' . $this->data['Attachment']['mime_type'];
				}
				echo $this->Html->link($imgUrl, $this->data['Attachment']['path'], array(
					'class' => 'thickbox pull-right',
				));
				$this->Form->inputDefaults(array(
					'class' => 'span6',
					'label' => false,
				));
				echo $this->Form->input('title', array(
					'label' => __d('croogo', 'Title'),
				));
				echo $this->Form->input('excerpt', array(
					'label' => __d('croogo', 'Caption'),
				));

				echo $this->Form->input('file_url', array(
					'label' => __d('croogo', 'File URL'),
					'value' => Router::url($this->data['Attachment']['path'], true),
					'readonly' => 'readonly')
				);

				echo $this->Form->input('file_type', array(
					'label' => __d('croogo', 'Mime Type'),
					'value' => $this->data['Attachment']['mime_type'],
					'readonly' => 'readonly')
				);

			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>

	<div class="span4">
	<?php
		$redirect = array('action' => 'index');
		if ($this->Session->check('Wysiwyg.redirect')) {
			$redirect = $this->Session->read('Wysiwyg.redirect');
		}
		echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
			$this->Form->button(__d('croogo', 'Save')) .
			$this->Html->link(
				__d('croogo', 'Cancel'),
				$redirect,
				array('class' => 'cancel', 'button' => 'danger')
			) .
			$this->Html->endBox();
	?>
	</div>
</div>
<?php echo $this->Form->end(); ?>