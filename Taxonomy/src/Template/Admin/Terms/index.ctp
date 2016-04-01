<?php

$this->assign('title', __d('croogo', 'Vocabulary: %s', $vocabulary->title));

$this->extend('Croogo/Core./Common/admin_index');

$this->Html
	->addCrumb(__d('croogo', 'Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Vocabularies'), array('plugin' => 'taxonomy', 'controller' => 'vocabularies', 'action' => 'index'))
	->addCrumb($vocabulary->title, array('plugin' => 'taxonomy', 'controller' => 'terms', 'action' => 'index', $vocabulary->id));

$this->append('actions');
	echo $this->Croogo->adminAction(
		__d('croogo', 'New Term'),
		array('action' => 'add', $vocabulary->id)
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
		$actions[] = $this->Croogo->adminRowActions($term->id);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'moveup',	$term->id, $vocabulary->id),
			array('icon' => $this->Theme->getIcon('move-up'), 'tooltip' => __d('croogo', 'Move up'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'movedown', $term->id, $vocabulary->id),
			array('icon' => $this->Theme->getIcon('move-down'), 'tooltip' => __d('croogo', 'Move down'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'edit', $term->id, $vocabulary->id),
			array('icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'delete', $term->id, $vocabulary->id),
			array('icon' => $this->Theme->getIcon('delete'), 'tooltip' => __d('croogo', 'Remove this item')),
			__d('croogo', 'Are you sure?'));
		$actions = $this->Html->div('item-actions', implode(' ', $actions));

		// Title Column
		$titleCol = $term->title;
		if (isset($defaultType['alias'])) {
			$titleCol = $this->Html->link($term->title, array(
				'prefix' => false,
				'plugin' => 'Croogo/Nodes',
				'controller' => 'Nodes',
				'action' => 'term',
				'type' => $defaultType['alias'],
				'slug' => $term->slug,
			), array(
				'target' => '_blank',
			));
		}

		if (!empty($term['Term']['indent'])):
			$titleCol = str_repeat('&emsp;', $term['Term']['indent']) . $titleCol;
		endif;

		// Build link list
		$typeLinks = $this->Taxonomies->generateTypeLinks($vocabulary->types, $term);
		if (!empty($typeLinks)) {
			$titleCol .= $this->Html->tag('small', $typeLinks);
		}

		$rows[] = array(
			'',
			$term->id,
			$titleCol,
			$term->slug,
			$actions,
		);
	endforeach;
	echo $this->Html->tableCells($rows);
$this->end();

?>
