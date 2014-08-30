<?php

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => $_icons['home']))
	->addCrumb(__d('croogo', 'Attachments'), '/' . $this->request->url);

$this->start('table-heading');
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Paginator->sort('id', __d('croogo', 'Id')),
		'&nbsp;',
		$this->Paginator->sort('title', __d('croogo', 'Title')),
		__d('croogo', 'URL'),
		__d('croogo', 'Actions'),
	));
	echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->append('table-body');
	$rows = array();
	foreach ($attachments as $attachment) {
		$actions = array();
		$actions[] = $this->Croogo->adminRowActions($attachment['Attachment']['id']);
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'attachments', 'action' => 'edit', $attachment['Attachment']['id']),
			array('icon' => $_icons['update'], 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'attachments', 'action' => 'delete', $attachment['Attachment']['id']),
			array('icon' => $_icons['delete'], 'tooltip' => __d('croogo', 'Remove this item')),
			__d('croogo', 'Are you sure?'));

		$mimeType = explode('/', $attachment['Attachment']['mime_type']);
		$imageType = $mimeType['1'];
		$mimeType = $mimeType['0'];
		$imagecreatefrom = array('gif', 'jpeg', 'png', 'string', 'wbmp', 'webp', 'xbm', 'xpm');
		if ($mimeType == 'image' && in_array($imageType, $imagecreatefrom)) {
			$imgUrl = $this->Image->resize('/uploads/' . $attachment['Attachment']['slug'], 100, 200, true, array('class' => 'img-polaroid', 'alt' => $attachment['Attachment']['title']));
			$thumbnail = $this->Html->link($imgUrl, $attachment['Attachment']['path'],
			array('escape' => false, 'class' => 'thickbox', 'title' => $attachment['Attachment']['title']));
		} else {
			$thumbnail = $this->Html->image('/croogo/img/icons/page_white.png', array('alt' => $attachment['Attachment']['mime_type'])) . ' ' . $attachment['Attachment']['mime_type'] . ' (' . $this->Filemanager->filename2ext($attachment['Attachment']['slug']) . ')';
		}

		$actions = $this->Html->div('item-actions', implode(' ', $actions));

		$rows[] = array(
			$attachment['Attachment']['id'],
			$thumbnail,
			$this->Html->tag('div', $attachment['Attachment']['title'], array('class' => 'ellipsis')),
			$this->Html->tag('div',
				$this->Html->link(
					$this->Html->url($attachment['Attachment']['path'], true),
					$attachment['Attachment']['path'],
					array('target' => '_blank')
				), array('class' => 'ellipsis')
			),
			$actions,
		);
	}

	echo $this->Html->tableCells($rows);

$this->end();
