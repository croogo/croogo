<?php

use Croogo\Core\Status;

$this->extend('Croogo/Core./Common/admin_index');

$this->CroogoHtml
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Menus'), '/' . $this->request->url);

$this->start('table-heading');
	$tableHeaders = $this->CroogoHtml->tableHeaders(array(
		$this->Paginator->sort('id', __d('croogo', 'Id')),
		$this->Paginator->sort('title', __d('croogo', 'Title')),
		$this->Paginator->sort('alias', __d('croogo', 'Alias')),
		$this->Paginator->sort('link_count', __d('croogo', 'Link Count')),
		$this->Paginator->sort('status', __d('croogo', 'Status')),
		__d('croogo', 'Actions'),
	));
	echo $this->CroogoHtml->tag('thead', $tableHeaders);
$this->end();

$this->start('table-body');
	$rows = array();
	foreach ($menus as $menu):
	$actions = array();
	$actions[] = $this->Croogo->adminRowAction(
		'',
		array('controller' => 'links', 'action' => 'index',	'?' => array('menu_id' => $menu['Menu']['id'])),
		array('icon' => $this->Theme->getIcon('inspect'), 'tooltip' => __d('croogo', 'View links'))
	);
	$actions[] = $this->Croogo->adminRowActions($menu->id);
	$actions[] = $this->Croogo->adminRowAction(
		'',
		array('controller' => 'menus', 'action' => 'edit', $menu->id),
		array('icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item'))
	);
	$actions[] = $this->Croogo->adminRowAction(
		'',
		array('controller' => 'menus', 'action' => 'delete', $menu->id),
		array('icon' => $this->Theme->getIcon('delete'), 'tooltip' => __d('croogo', 'Remove this item')),
		__d('croogo', 'Are you sure?')
	);
	$actions = $this->CroogoHtml->div('item-actions', implode(' ', $actions));

	$title = $this->CroogoHtml->link($menu->title, array(
		'controller' => 'links',
		'?' => array(
			'menu_id' => $menu->id
		)
	));

	if ($menu->status === Status::PREVIEW) {
		$title .= ' ' . $this->CroogoHtml->tag('span', __d('croogo', 'preview'),
			array('class' => 'label label-warning')
			);
	}

	$status = $this->element('admin/toggle', array(
		'id' => $menu->id,
		'status' => $menu->status,,
	));

	$rows[] = array(
		$menu->id,
		$title,
		$menu->alias,
		$menu->link_account,
		$status,
		$this->CroogoHtml->div('item-actions', $actions),
	);
endforeach;

echo $this->CroogoHtml->tableCells($rows);

$this->end();
