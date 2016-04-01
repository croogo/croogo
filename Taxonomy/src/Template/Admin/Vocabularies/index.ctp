<?php

$this->extend('Croogo/Core./Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Content'), array('plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Vocabularies'), '/' . $this->request->url);

$this->start('table-heading');
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Paginator->sort('id', __d('croogo', 'Id')),
		$this->Paginator->sort('title', __d('croogo', 'Title')),
		$this->Paginator->sort('alias', __d('croogo', 'Alias')),
		$this->Paginator->sort('plugin', __d('croogo', 'Plugin')),
		__d('croogo', 'Actions'),
	));

	echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->append('table-body');

	$rows = array();
	foreach ($vocabularies as $vocabulary) :
		$actions = array();
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'Terms', 'action' => 'index', $vocabulary->id),
			array('icon' => $this->Theme->getIcon('inspect'), 'tooltip' => __d('croogo', 'View terms'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'moveup', $vocabulary->id),
			array('icon' => $this->Theme->getIcon('move-up'), 'tooltip' => __d('croogo', 'Move up'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'movedown', $vocabulary->id),
			array('icon' => $this->Theme->getIcon('move-down'), 'tooltip' => __d('croogo', 'Move down'))
		);
		$actions[] = $this->Croogo->adminRowActions($vocabulary->id);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'edit', $vocabulary->id),
			array('icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'delete', $vocabulary->id),
			array('icon' => $this->Theme->getIcon('delete'), 'tooltip' => __d('croogo', 'Remove this item')),
			__d('croogo', 'Are you sure?'));
		$actions = $this->Html->div('item-actions', implode(' ', $actions));
		$rows[] = array(
			$vocabulary->id,
			$this->Html->link($vocabulary->title, array('controller' => 'Terms', 'action' => 'index', $vocabulary->id)),
			$vocabulary->alias,
			$vocabulary->plugin,
			$actions,
		);
	endforeach;

	echo $this->Html->tableCells($rows);

$this->end();
