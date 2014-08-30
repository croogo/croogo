<?php

$this->Croogo->adminscript('Menus.admin');

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => $_icons['home']))
	->addCrumb(__d('croogo', 'Menus'), array('plugin' => 'menus', 'controller' => 'menus', 'action' => 'index'))
	->addCrumb(__d('croogo', $menu['Menu']['title']), array(
		'plugin' => 'menus', 'controller' => 'links', 'action' => 'index',
		'?' => array('menu_id' => $menu['Menu']['id'])));

$this->append('actions');

	echo $this->Croogo->adminAction(
		__d('croogo', 'New %s', __d('croogo', Inflector::singularize($this->name))),
		array('action' => 'add', $menu['Menu']['id']),
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
			$menu['Menu']['id'],
		),
	)));

$this->start('table-heading');
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Form->checkbox('checkAll'),
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
			'controller' => 'links', 'action' => 'moveup', $linkId
			), array(
			'icon' => $_icons['move-up'],
			'tooltip' => __d('croogo', 'Move up'),
		));
		$actions[] = $this->Croogo->adminRowAction('', array(
			'controller' => 'links', 'action' => 'movedown', $linkId,
			), array(
			'icon' => $_icons['move-down'],
			'tooltip' => __d('croogo', 'Move down'),
		));
		$actions[] = $this->Croogo->adminRowActions($linkId);
		$actions[] = $this->Croogo->adminRowAction('', array(
			'controller' => 'links', 'action' => 'edit', $linkId,
			), array(
			'icon' => $_icons['update'], 'tooltip' => __d('croogo', 'Edit this item'),
		));

		$actions[] = $this->Croogo->adminRowAction('',
			'#Link' . $linkId . 'Id',
			array(
				'icon' => $_icons['copy'],
				'tooltip' => __d('croogo', 'Create a copy'),
				'rowAction' => 'copy',
			),
			__d('croogo', 'Create a copy of this Link?')
		);

		$actions[] = $this->Croogo->adminRowAction('', '#Link' . $linkId . 'Id',
			array(
				'icon' => $_icons['delete'],
				'class' => 'delete',
				'tooltip' => __d('croogo', 'Delete this item'),
				'rowAction' => 'delete',
			),
			__d('croogo', 'Are you sure?')
		);
		$actions = $this->Html->div('item-actions', implode(' ', $actions));

		if ($linksStatus[$linkId] == CroogoStatus::PREVIEW) {
			$linkTitle .= ' ' . $this->Html->tag('span', __d('croogo', 'preview'),
			array('class' => 'label label-warning')
			);
		}

		$rows[] = array(
			$this->Form->checkbox('Link.' . $linkId . '.id', array('class' => 'row-select')),
			$linkId,
			$linkTitle,
			$this->element('admin/toggle', array(
				'id' => $linkId,
				'status' => (int)$linksStatus[$linkId],
			)),
			$actions,
		);
	endforeach;

	echo $this->Html->tableCells($rows);

$this->end();

$this->start('bulk-action');
	echo $this->Form->input('Link.action', array(
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
$button = $this->Form->button(__d('croogo', 'Submit'), array(
		'type' => 'submit',
		'value' => 'submit',
	));
echo $this->Html->div('controls', $button);

$this->end();

$this->append('form-end',$this->Form->end());
