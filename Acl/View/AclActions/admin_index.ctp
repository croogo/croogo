<?php

$this->extend('/Common/admin_index');
$this->name = 'acos';
$this->Html->script('/acl/js/acl_permissions.js', false);
$this->Html->scriptBlock("$(document).ready(function(){ AclPermissions.documentReady(); });", array('inline' => false));

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Users'), array('plugin' => 'users', 'controller' => 'users', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Permissions'), array(
		'plugin' => 'acl', 'controller' => 'acl_permissions',
	))
	->addCrumb(__d('croogo', 'Actions'), array('plugin' => 'acl', 'controller' => 'acl_actions', 'action' => 'index', 'permission' => 1));

?>
<?php $this->start('actions'); ?>
<li class="btn-group">
<?php
	echo $this->Html->link(
		__d('croogo', 'Tools') . ' ' . '<span class="caret"></span>',
		'#',
		array(
			'class' => 'btn dropdown-toggle',
			'data-toggle' => 'dropdown',
		)
	);

	$generateUrl = array(
		'plugin' => 'acl',
		'controller' => 'acl_actions',
		'action' => 'generate',
		'permissions' => 1
	);
	$out = $this->Croogo->adminAction(__d('croogo', 'Generate'),
		$generateUrl,
		array(
			'button' => false,
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
			'method' => 'post',
			'tooltip' => array(
				'data-title' => __d('croogo', 'Create new & remove orphaned actions'),
				'data-placement' => 'right',
			),
		)
	);
	echo $this->Html->tag('ul', $out, array('class' => 'dropdown-menu'));
?>
</li>
<?php
	echo $this->Croogo->adminAction(__d('croogo', 'Edit Actions'),
		array('controller' => 'acl_actions', 'action' => 'index', 'permissions' => 1)
	);
?>
<?php $this->end(); ?>

<table class="table permission-table">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		__d('croogo', 'Id'),
		__d('croogo', 'Alias'),
		__d('croogo', 'Actions'),
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
<?php

	$currentController = '';
	$icon = '<i class="icon-none pull-right"></i>';
	foreach ($acos as $acoIndex => $aco) {
		$id = $aco['Aco']['id'];
		$alias = $aco['Aco']['alias'];
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
			if ($aco['Aco']['children'] > 0) {
				$class .= ' perm-expand';
			}
			$oddOptions = array();
			$evenOptions = array();
			$currentController = $alias;
		}

		$actions = array();
		$actions[] = $this->Html->link('',
			array('action' => 'move', $id, 'up'),
			array('icon' => 'chevron-up', 'tooltip' => __d('croogo', 'Move up'))
		);
		$actions[] = $this->Html->link('',
			array('action' => 'move', $id, 'down'),
			array('icon' => 'chevron-down', 'tooltip' => __d('croogo', 'Move down'))
		);

		$actions[] = $this->Html->link('',
			array('action' => 'edit', $id),
			array('icon' => 'pencil', 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Form->postLink('',
			array('action' => 'delete',	$id),
			array('icon' => 'trash', 'tooltip' => __d('croogo', 'Remove this item')),
			__d('croogo', 'Are you sure?')
		);
		$actions = $this->Html->div('item-actions', implode(' ', $actions));
		$row = array(
			$id,
			$this->Html->div(trim($class), $alias . $icon, array(
				'data-id' => $id,
				'data-alias' => $alias,
				'data-level' => $level,
			)),
			$actions,
		);

		echo $this->Html->tableCells(array($row), $oddOptions, $evenOptions);
	}
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
<?php
?>
</table>
