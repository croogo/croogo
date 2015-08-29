<?php

$this->extend('Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Extensions'), array('plugin' => 'extensions', 'controller' => 'extensions_plugins', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Locales'), '/' . $this->request->url);

$this->append('actions');
	echo $this->Croogo->adminAction(__d('croogo', 'Upload'),
		array('action' => 'add')
	);
	echo $this->Croogo->adminAction('Reset Locale',
		array('action' => 'reset_locale'),
		array('method' => 'post')
	);
$this->end();

$this->start('table-heading');
	$tableHeaders = $this->Html->tableHeaders(array(
		'',
		__d('croogo', 'Locale'),
		__d('croogo', 'Default'),
		__d('croogo', 'Status'),
		__d('croogo', 'Actions'),
	));
	echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->append('table-body');
	$rows = array();
	foreach ($locales as $i => $locale):
		$actions = array();
		$language = null;
		if (isset($languages[$i]['language'])) {
			$language = $languages[$i]['language'];
		}

		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'activate', $locale),
			array('icon' => $this->Theme->getIcon('power-on'), 'tooltip' => __d('croogo', 'Activate'), 'method' => 'post')
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'edit', $locale),
			array('icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'delete', $locale),
			array('icon' => $this->Theme->getIcon('delete'), 'tooltip' => __d('croogo', 'Remove this item')),
			__d('croogo', 'Are you sure?')
		);

		$actions = $this->Html->div('item-actions', implode(' ', $actions));
		if ($locale == Configure::read('Site.locale')) {
			$status = $this->Html->status(1);
		} else {
			$status = $this->Html->status(0);
		}

		$rows[] = array(
			'',
			$locale,
			$language,
			$status,
			$actions,
		);
	endforeach;

	echo $this->Html->tableCells($rows);
$this->end();
?>
