<?php
$this->extend('/Common/admin_index');
$this->name = 'acl_permissions';
$this->Html->script('/acl/js/acl_permissions.js', false);
?>

<?php $this->start('tabs'); ?>
<li><?php echo $this->Form->postLink(__('Generate Actions'), array('controller' => 'acl_actions', 'action'=>'generate', 'permissions' => 1)); ?></li>
<li><?php echo $this->Html->link(__('Edit Actions'), array('controller' => 'acl_actions', 'action'=>'index', 'permissions' => 1)); ?></li>
<?php $this->end(); ?>

<table class="permission-table" cellpadding="0" cellspacing="0">
<?php
	$roleTitles = array_values($roles);
	$roleIds   = array_keys($roles);

	$tableHeaders = array(
		__('Id'),
		__('Alias'),
	);
	$tableHeaders = array_merge($tableHeaders, $roleTitles);
	$tableHeaders =  $this->Html->tableHeaders($tableHeaders);
	echo $tableHeaders;

	$currentController = '';
	foreach ($acos AS $index => $aco) {
		$id = $aco['Aco']['id'];
		$alias = $aco['Aco']['alias'];
		$class = '';
		if(substr($alias, 0, 1) == '_') {
			$level = 1;
			$class .= 'level-' . $level;
			$oddOptions = array('class' => 'hidden controller-' . $currentController);
			$evenOptions = array('class' => 'hidden controller-' . $currentController);
			$alias = substr_replace($alias, '', 0, 1);
		} else {
			$level = 0;
			$class .= ' controller';
			if ($aco['Aco']['children'] > 0) {
				$class .= ' expand';
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

		foreach ($roles AS $roleId => $roleTitle) {
			$row[] = '';
		}

		echo $this->Html->tableCells(array($row), $oddOptions, $evenOptions);
	}

	echo $tableHeaders;
?>
</table>
