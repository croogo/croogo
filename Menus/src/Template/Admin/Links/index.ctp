<?php

use Cake\Utility\Inflector;
use Croogo\Core\Status;

$this->Croogo->adminscript('Croogo/Menus.admin');

$this->extend('Croogo/Core./Common/admin_index');

$this->Html
	->addCrumb(__d('croogo', 'Menus'), ['controller' => 'Menus', 'action' => 'index'])
	->addCrumb(__d('croogo', $menu->title), array(
		'action' => 'index',
		'?' => array('menu_id' => $menu->id)));

$this->append('actions');

	echo $this->Croogo->adminAction(
		__d('croogo', 'New link'),
		array('action' => 'add', $menu->id),
		array('button' => 'success')
	);
$this->end();

	if (isset($this->request->params['named'])) {
		foreach ($this->request->params['named'] as $nn => $nv) {
			$this->Paginator->options['url'][] = $nn . ':' . $nv;
		}
	}

	$this->append('form-start', $this->Form->create('Links', array(
		'url' => array(
			'action' => 'process',
			$menu->id,
		),
	)));

$this->start('table-heading');
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Form->checkbox('checkAll', ['id' => 'LinksCheckAll']),
		__d('croogo', 'Id'),
		__d('croogo', 'Title'),
		__d('croogo', 'Status'),
		__d('croogo', 'Actions'),
	));
	echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->append('table-body');

	$rows = array();
	foreach ($linksTree as $linkId => $linkTitle):
		$actions = array();
		$actions[] = $this->Croogo->adminRowAction('', array(
			'action' => 'moveUp', $linkId
			), array(
			'icon' => $this->Theme->getIcon('move-up'),
			'tooltip' => __d('croogo', 'Move up'),
		));
		$actions[] = $this->Croogo->adminRowAction('', array(
			'action' => 'moveDown', $linkId,
			), array(
			'icon' => $this->Theme->getIcon('move-down'),
			'tooltip' => __d('croogo', 'Move down'),
		));
		$actions[] = $this->Croogo->adminRowActions($linkId);
		$actions[] = $this->Croogo->adminRowAction('', array(
			'action' => 'edit', $linkId,
			), array(
			'icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item'),
		));

		$actions[] = $this->Croogo->adminRowAction('',
			'#Link' . $linkId . 'Id',
			array(
				'icon' => $this->Theme->getIcon('copy'),
				'tooltip' => __d('croogo', 'Create a copy'),
				'rowAction' => 'copy',
			),
			__d('croogo', 'Create a copy of this Link?')
		);

		$actions[] = $this->Croogo->adminRowAction('', '#Link' . $linkId . 'Id',
			array(
				'icon' => $this->Theme->getIcon('delete'),
				'class' => 'delete',
				'tooltip' => __d('croogo', 'Delete this item'),
				'rowAction' => 'delete',
			),
			__d('croogo', 'Are you sure?')
		);
		$actions = $this->Html->div('item-actions', implode(' ', $actions));

		if ($linksStatus[$linkId] == Status::PREVIEW) {
			$linkTitle .= ' ' . $this->Html->tag('span', __d('croogo', 'preview'),
			array('class' => 'label label-warning')
			);
		}

		$rows[] = array(
			$this->Form->checkbox('Links.' . $linkId . '.id', array('class' => 'row-select')),
			$linkId,
			$linkTitle,
			$this->element('Croogo/Core.admin/toggle', array(
				'id' => $linkId,
				'status' => (int)$linksStatus[$linkId],
			)),
			$actions,
		);
	endforeach;

	echo $this->Html->tableCells($rows);

$this->end();

$this->start('bulk-action');
	echo $this->Form->input('Links.action', array(
		'div' => 'input inline',
		'label' => false,
		'options' => array(
			'publish' => __d('croogo', 'Publish'),
			'unpublish' => __d('croogo', 'Unpublish'),
			'delete' => __d('croogo', 'Delete'),
			array(
				'value' => 'copy',
				'text' => __d('croogo', 'Copy'),
				'hidden' => true,
			),
		),
		'empty' => true,
	));
	$button = $this->Form->button(__d('croogo', 'Submit'), array(
		'type' => 'submit',
		'value' => 'submit',
	));
	echo $this->Html->div('controls', $button);
$this->end();

$this->append('form-end',$this->Form->end());
?>
