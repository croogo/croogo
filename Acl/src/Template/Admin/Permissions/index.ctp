<?php

$this->extend('Croogo/Core./Common/admin_index');

$this->name = 'acos';

$this->Html->script('Croogo/Acl.acl_permissions', ['block' => true]);

$this->Croogo->adminScript('Croogo/Acl.acl_permissions');

$this->Html
	->addCrumb(__d('croogo', 'Users'), array('plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Permissions'), array(
		'plugin' => 'Croogo/Acl', 'controller' => 'Permissions',
	));

$this->append('actions');
	$toolsButton = $this->Html->link(
		__d('croogo', 'Tools') . ' ' . '<span class="caret"></span>',
		'#',
		array(
			'button' => 'default',
			'class' => 'dropdown-toggle',
			'data-toggle' => 'dropdown',
			'escape' => false
		)
	);

	$generateUrl = array(
		'plugin' => 'Croogo/Acl',
		'controller' => 'Actions',
		'action' => 'generate',
		'permissions' => 1
	);
	$out = $this->Croogo->adminAction(__d('croogo', 'Generate'),
		$generateUrl,
		array(
			'button' => false,
			'list' => true,
			'method' => 'post',
			'tooltip' => array(
				'data-title' => __d('croogo', 'Create new actions (no removal)'),
				'data-placement' => 'right',
			),
		)
	);
	$out .= $this->Croogo->adminAction(__d('croogo', 'Synchronize'),
		$generateUrl + array('sync' => 1),
		array(
			'button' => false,
			'list' => true,
			'method' => 'post',
			'tooltip' => array(
				'data-title' => __d('croogo', 'Create new & remove orphaned actions'),
				'data-placement' => 'right',
			),
		)
	);
	echo $this->Html->div('btn-group',
		$toolsButton .
		$this->Html->tag('ul', $out, array('class' => 'dropdown-menu'))
	);

	echo $this->Croogo->adminAction(__d('croogo', 'Edit Actions'),
		array('controller' => 'Actions', 'action' => 'index', 'permissions' => 1)
	);
$this->end();

$this->set('tableClass', 'table permission-table');
$this->start('table-heading');
	$roleTitles = array_values($roles->toArray());
	$roleIds = array_keys($roles->toArray());

	$tableHeaders = array(
		__d('croogo', 'Id'),
		__d('croogo', 'Alias'),
	);
	$tableHeaders = array_merge($tableHeaders, $roleTitles);
	$tableHeaders = $this->Html->tableHeaders($tableHeaders);
$this->end();

$this->append('table-body');
	$currentController = '';
	$icon = '<i class="icon-none pull-right"></i>';
	foreach ($acos as $aco) {
		$id = $aco->id;
		$alias = $aco->alias;
		$class = '';
		if (substr($alias, 0, 1) == '_') {
			$level = 1;
			$class .= 'level-' . $level;
			$oddOptions = array('class' => 'hidden controller-' . $currentController);
			$evenOptions = array('class' => 'hidden controller-' . $currentController);
			$alias = substr_replace($alias, '', 0, 1);
		} else {
			$level = 0;
			$class .= ' controller';
			if ($aco->children > 0) {
				$class .= ' perm-expand';
			}
			$oddOptions = array();
			$evenOptions = array();
			$currentController = $alias;
		}

		$row = array(
			$id,
			$this->Html->div(trim($class), $alias . $icon, array(
				'data-id' => $id,
				'data-alias' => $alias,
				'data-level' => $level,
			)),
		);

		foreach ($roles as $roleId => $roleTitle) {
			$row[] = '';
		}

		echo $this->Html->tableCells($row, $oddOptions, $evenOptions);
	}
	echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->Js->buffer('AclPermissions.documentReady();');
