<?php

$this->extend('/Common/admin_index');
$this->name = 'acl_permissions';
$this->Html->script('/acl/js/acl_permissions.js', false);

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Users'), array('plugin' => 'users', 'controller' => 'users', 'action' => 'index'))
	->addCrumb(__('Permissions'), array(
		'plugin' => 'acl', 'controller' => 'acl_permissions',
	));

?>
<?php $this->start('actions'); ?>
<li>
<?php
	echo $this->Form->postLink(__('Generate Actions'),
		array('controller' => 'acl_actions', 'action' => 'generate', 'permissions' => 1),
		array('button' => 'default')
	);
?>
</li>
<li>
<?php
	echo $this->Form->postLink(__('Sync Actions'),
		array('controller' => 'acl_actions', 'action' => 'generate', 'permissions' => 1, 'sync' => 1),
		array('button' => 'default')
	);
?>
</li>
<li>
<?php
	echo $this->Html->link(__('Edit Actions'),
		array('controller' => 'acl_actions', 'action'=>'index', 'permissions' => 1),
		array('button' => 'default')
	);
?>
</li>
<?php $this->end(); ?>

<table class="table permission-table">
<?php
	$roleTitles = array_values($roles);
	$roleIds   = array_keys($roles);

	$tableHeaders = array(
		__('Id'),
		__('Alias'),
	);
	$tableHeaders = array_merge($tableHeaders, $roleTitles);
	$tableHeaders =  $this->Html->tableHeaders($tableHeaders);
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
<?php

	$currentController = '';
	foreach ($acos as $index => $aco) {
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

		$row = array(
			$id,
			$this->Html->div(trim($class), $alias, array(
				'data-id' => $id,
				'data-alias' => $alias,
				'data-level' => $level,
				)),
		);

		foreach ($roles as $roleId => $roleTitle) {
			$row[] = '';
		}

		echo $this->Html->tableCells(array($row), $oddOptions, $evenOptions);
	}

?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>

</table>
