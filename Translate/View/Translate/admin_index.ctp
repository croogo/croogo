<?php

$this->extend('/Common/admin_index');
$this->name = 'translate';

$plugin = $controller = 'nodes';
if (isset($this->request->params['models'][$modelAlias])):
	$plugin = $this->request->params['models'][$modelAlias]['plugin'];
	$controller = strtolower(Inflector::pluralize($modelAlias));
endif;

$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(
		Inflector::pluralize($modelAlias),
		array(
			'plugin' => Inflector::underscore($plugin),
			'controller' => Inflector::underscore($controller),
			'action' => 'index',
		)
	)
	->addCrumb(
		$record[$modelAlias][$displayField],
		array(
			'plugin' => Inflector::underscore($plugin),
			'controller' =>  Inflector::underscore($controller),
			'action' => 'edit',
			$record[$modelAlias]['id'],
		)
	)
	->addCrumb(__d('croogo', 'Translations'), '/' . $this->request->url);

$this->start('actions');
	echo $this->Croogo->adminAction(
		__d('croogo', 'Translate in a new language'),
		array(
			'plugin' => 'settings',
			'controller' => 'languages',
			'action' => 'select',
			$record[$modelAlias]['id'],
			$modelAlias
		),
		array(
			'button' => 'default',
		)
	);
$this->end();

if (count($translations) == 0):
	echo $this->Html->para(null, __d('croogo', 'No translations available.'));
	return;
endif;

$this->append('table-heading');
	$tableHeaders = $this->Html->tableHeaders(array(
		'',
		__d('croogo', 'Title'),
		__d('croogo', 'Locale'),
		__d('croogo', 'Actions'),
	));
	echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->append('table-body');
	$rows = array();
	foreach ($translations as $translation):
		$actions = array();
		$actions[] = $this->Croogo->adminRowAction('', array(
			'action' => 'edit',
			$id,
			$modelAlias,
			'locale' => $translation[$runtimeModelAlias]['locale'],
		), array(
			'icon' => $this->Theme->getIcon('update'),
			'tooltip' => __d('croogo', 'Edit this item'),
		));
		$actions[] = $this->Croogo->adminRowAction('', array(
			'action' => 'delete',
			$id,
			$modelAlias,
			$translation[$runtimeModelAlias]['locale'],
		), array(
			'icon' => $this->Theme->getIcon('delete'),
			'tooltip' => __d('croogo', 'Remove this item'),
		), __d('croogo', 'Are you sure?'));

		$actions = $this->Html->div('item-actions', implode(' ', $actions));
		$rows[] = array(
			'',
			$translation[$runtimeModelAlias]['content'],
			$translation[$runtimeModelAlias]['locale'],
			$actions,
		);
	endforeach;

	echo $this->Html->tableCells($rows);
$this->end();
