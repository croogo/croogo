<?php

$this->extend('Croogo/Core./Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
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
		$actions[] = $this->Croogo->adminRowActions($attachment->id);
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'Attachments', 'action' => 'edit', $attachment->id),
			array('icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'attachments', 'action' => 'delete', $attachment->id),
			array('icon' => $this->Theme->getIcon('delete'), 'tooltip' => __d('croogo', 'Remove this item')),
			__d('croogo', 'Are you sure?'));

		$mimeType = explode('/', $attachment->mime_type);
		$imageType = $mimeType['1'];
		$mimeType = $mimeType['0'];
		$imagecreatefrom = array('gif', 'jpeg', 'png', 'string', 'wbmp', 'webp', 'xbm', 'xpm');
		if ($mimeType == 'image' && in_array($imageType, $imagecreatefrom)) {
			$imgUrl = $this->Image->resize('/uploads/' . $attachment->slug, 100, 200, true, array('alt' => $attachment->title));
			$thumbnail = $this->Html->link($imgUrl, $attachment->path,
			array('escape' => false, 'class' => 'thickbox', 'title' => $attachment->title));
		} else {
			$thumbnail = $this->Html->thumbnail('/croogo/img/icons/page_white.png', array('alt' => $attachment->mime_type)) . ' ' . $attachment->mime_type . ' (' . $this->Filemanager->filename2ext($attachment->slug) . ')';
		}

		$actions = $this->Html->div('item-actions', implode(' ', $actions));

		$rows[] = array(
			$attachment->id,
			$thumbnail,
			$this->Html->tag('div', $attachment->title, array('class' => 'ellipsis')),
			$this->Html->tag('div',
				$this->Html->link(
					$this->Url->build($attachment->path, true),
					$attachment->path,
					array('target' => '_blank')
				), array('class' => 'ellipsis')
			),
			$actions,
		);
	}

	echo $this->Html->tableCells($rows);

$this->end();
