<?php

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Attachments'), $this->here);

?>
<table class="table table-striped">
<?php

	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Paginator->sort('id'),
		'&nbsp;',
		$this->Paginator->sort('title'),
		__('URL'),
		__('Actions'),
	));

?>
	<thead>
	<?php echo $tableHeaders; ?>
	</thead>
<?php

	$rows = array();
	foreach ($attachments as $attachment) {
		$actions = array();
		$actions[] = $this->Croogo->adminRowActions($attachment['Node']['id']);
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'attachments', 'action' => 'edit', $attachment['Node']['id']),
			array('icon' => 'pencil', 'tooltip' => __('Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'attachments', 'action' => 'delete', $attachment['Node']['id']),
			array('icon' => 'trash', 'tooltip' => __('Remove this item')),
			__('Are you sure?'));

		$mimeType = explode('/', $attachment['Node']['mime_type']);
		$mimeType = $mimeType['0'];
		if ($mimeType == 'image') {
			$imgUrl = $this->Image->resize('/uploads/' . $attachment['Node']['slug'], 100, 200, true, array('class' => 'img-polaroid'));
			$thumbnail = $this->Html->link($imgUrl, $attachment['Node']['path'],
			array('escape' => false, 'class' => 'thickbox', 'title' => $attachment['Node']['title']));
		} else {
			$thumbnail = $this->Html->image('/img/icons/page_white.png') . ' ' . $attachment['Node']['mime_type'] . ' (' . $this->Filemanager->filename2ext($attachment['Node']['slug']) . ')';
		}

		$actions = $this->Html->div('item-actions', implode(' ', $actions));

		$rows[] = array(
			$attachment['Node']['id'],
			$thumbnail,
			$attachment['Node']['title'],
			$this->Html->link(
				$this->Html->url($attachment['Node']['path'], true),
				$attachment['Node']['path'],
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
