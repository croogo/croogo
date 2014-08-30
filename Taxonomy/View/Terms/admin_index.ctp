<?php

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => $_icons['home']))
	->addCrumb(__d('croogo', 'Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Vocabularies'), array('plugin' => 'taxonomy', 'controller' => 'vocabularies', 'action' => 'index'))
	->addCrumb($vocabulary['Vocabulary']['title'], array('plugin' => 'taxonomy', 'controller' => 'terms', 'action' => 'index', $vocabulary['Vocabulary']['id']));

$this->append('actions');
	echo $this->Croogo->adminAction(
		__d('croogo', 'New Term'),
		array('action' => 'add', $vocabulary['Vocabulary']['id'])
	);
$this->end();

	if (isset($this->request->params['named'])) {
		foreach ($this->request->params['named'] as $nn => $nv) {
			$this->Paginator->options['url'][] = $nn . ':' . $nv;
		}
	}
$this->start('table-heading');
	$tableHeaders = $this->Html->tableHeaders(array(
		'',
		__d('croogo', 'Id'),
		__d('croogo', 'Title'),
		__d('croogo', 'Slug'),
		__d('croogo', 'Actions'),
	));
	echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->append('table-body');
	$rows = array();

	foreach ($terms as $term):
		$actions = array();
		$actions[] = $this->Croogo->adminRowActions($term['Term']['id']);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'moveup',	$term['Term']['id'], $vocabulary['Vocabulary']['id']),
			array('icon' => $_icons['move-up'], 'tooltip' => __d('croogo', 'Move up'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'movedown', $term['Term']['id'], $vocabulary['Vocabulary']['id']),
			array('icon' => $_icons['move-down'], 'tooltip' => __d('croogo', 'Move down'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'edit', $term['Term']['id'], $vocabulary['Vocabulary']['id']),
			array('icon' => $_icons['update'], 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'delete', $term['Term']['id'], $vocabulary['Vocabulary']['id']),
			array('icon' => $_icons['delete'], 'tooltip' => __d('croogo', 'Remove this item')),
			__d('croogo', 'Are you sure?'));
		$actions = $this->Html->div('item-actions', implode(' ', $actions));

		// Title Column
		$titleCol = $term['Term']['title'];
		if (isset($defaultType['alias'])) {
			$titleCol = $this->Html->link($term['Term']['title'], array(
				'plugin' => 'nodes',
				'controller' => 'nodes',
				'action' => 'term',
				'type' => $defaultType['alias'],
				'slug' => $term['Term']['slug'],
				'admin' => 0,
			), array(
				'target' => '_blank',
			));
		}

		if (!empty($term['Term']['indent'])):
			$titleCol = str_repeat('&emsp;', $term['Term']['indent']) . $titleCol;
		endif;

		// Build link list
		$typeLinks = $this->Taxonomies->generateTypeLinks($vocabulary['Type'], $term);
		if (!empty($typeLinks)) {
			$titleCol .= $this->Html->tag('small', $typeLinks);
		}

		$rows[] = array(
			'',
			$term['Term']['id'],
			$titleCol,
			$term['Term']['slug'],
			$actions,
		);
	endforeach;
	echo $this->Html->tableCells($rows);
$this->end();

?>
