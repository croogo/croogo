<?php

use Cake\Utility\Inflector;
use Croogo\Core\CroogoStatus;

$this->Croogo->adminscript('Croogo/Menus.admin');

$this->extend('Croogo/Core./Common/admin_index');

$this->CroogoHtml
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
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

	$this->append('form-start', $this->Form->create('Link', array(
		'url' => array(
			'action' => 'process',
			$menu->id,
		),
	)));

$this->start('table-heading');
	$tableHeaders = $this->CroogoHtml->tableHeaders(array(
		$this->CroogoForm->checkbox('checkAll'),
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
		$actions = $this->CroogoHtml->div('item-actions', implode(' ', $actions));

		if ($linksStatus[$linkId] == CroogoStatus::PREVIEW) {
			$linkTitle .= ' ' . $this->CroogoHtml->tag('span', __d('croogo', 'preview'),
			array('class' => 'label label-warning')
			);
		}

		$rows[] = array(
			$this->CroogoForm->checkbox('Link.' . $linkId . '.id', array('class' => 'row-select')),
			$linkId,
			$linkTitle,
			$this->element('Croogo/Core.admin/toggle', array(
				'id' => $linkId,
				'status' => (int)$linksStatus[$linkId],
			)),
			$actions,
		);
	endforeach;

	echo $this->CroogoHtml->tableCells($rows);

$this->end();

$this->start('bulk-action');
	echo $this->CroogoForm->input('Link.action', array(
		'div' => 'input inline',
		'label' => false,
		'options' => array(
			'publish' => __d('croogo', 'Publish'),
			'unpublish' => __d('croogo', 'Unpublish'),
			'delete' => __d('croogo', 'Delete'),
			'copy' => array(
				'value' => 'copy',
				'name' => __d('croogo', 'Copy'),
				'hidden' => true,
			),
		),
		'empty' => true,
	));
	$button = $this->CroogoForm->button(__d('croogo', 'Submit'), array(
		'type' => 'submit',
		'value' => 'submit',
	));
	echo $this->CroogoHtml->div('controls', $button);
$this->end();

$this->append('form-end',$this->CroogoForm->end());
?>
