<?php

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Attachments'), '/' . $this->request->url);

?>
<table class="table table-striped">
<?php

	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Paginator->sort('id', __d('croogo', 'Id')),
		'&nbsp;',
		$this->Paginator->sort('title', __d('croogo', 'Title')),
		__d('croogo', 'URL'),
		__d('croogo', 'Actions'),
	));

?>
	<thead>
	<?php echo $tableHeaders; ?>
	</thead>
<?php

	$rows = array();
	foreach ($attachments as $attachment) {
		$actions = array();
		$actions[] = $this->Croogo->adminRowActions($attachment['Attachment']['id']);
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'attachments', 'action' => 'edit', $attachment['Attachment']['id']),
			array('icon' => 'pencil', 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'attachments', 'action' => 'delete', $attachment['Attachment']['id']),
			array('icon' => 'trash', 'tooltip' => __d('croogo', 'Remove this item')),
			__d('croogo', 'Are you sure?'));

		$mimeType = explode('/', $attachment['Attachment']['mime_type']);
		$mimeType = $mimeType['0'];
		if ($mimeType == 'image') {
			$imgUrl = $this->Image->resize('/uploads/' . $attachment['Attachment']['slug'], 100, 200, true, array('class' => 'img-polaroid'));
			$thumbnail = $this->Html->link($imgUrl, $attachment['Attachment']['path'],
			array('escape' => false, 'class' => 'thickbox', 'title' => $attachment['Attachment']['title']));
		} else {
			$thumbnail = $this->Html->image('/croogo/img/icons/page_white.png') . ' ' . $attachment['Attachment']['mime_type'] . ' (' . $this->Filemanager->filename2ext($attachment['Attachment']['slug']) . ')';
		}

		$actions = $this->Html->div('item-actions', implode(' ', $actions));

		$rows[] = array(
			$attachment['Attachment']['id'],
			$thumbnail,
			$attachment['Attachment']['title'],
			$this->Html->link(
				$this->Html->url($attachment['Attachment']['path'], true),
				$attachment['Attachment']['path'],
				array(
					'target' => '_blank',
				)
			),
			$actions,
		);
	}

	echo $this->Html->tableCells($rows);

?>
</table>
