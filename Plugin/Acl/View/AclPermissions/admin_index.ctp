<?php
$this->extend('/Common/admin_index');
$this->name = 'acl_permissions';
$this->Html->script('/acl/js/acl_permissions.js', false);
?>

<?php $this->start('tabs'); ?>
<li><?php echo $this->Form->postLink(__('Generate Actions'), array('controller' => 'acl_actions', 'action'=>'generate', 'permissions' => 1)); ?></li>
<li><?php echo $this->Html->link(__('Edit Actions'), array('controller' => 'acl_actions', 'action'=>'index', 'permissions' => 1)); ?></li>
<?php $this->end(); ?>

<table cellpadding="0" cellspacing="0">
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
	foreach ($acos AS $id => $alias) {
		$class = '';
		if(substr($alias, 0, 1) == '_') {
			$level = 1;
			$class .= 'level-'.$level;
			$oddOptions = array('class' => 'hidden controller-'.$currentController);
			$evenOptions = array('class' => 'hidden controller-'.$currentController);
			$alias = substr_replace($alias, '', 0, 1);
		} else {
			$level = 0;
			$class .= ' controller expand';
			$oddOptions = array();
			$evenOptions = array();
			$currentController = $alias;
		}

		$row = array(
			$id,
			$this->Html->div($class, $alias),
		);

		foreach ($roles AS $roleId => $roleTitle) {
			if ($level != 0) {
				if ($roleId != 1) {
					if ($permissions[$id][$roleId] == 1) {
						$row[] = $this->Html->image('/img/icons/tick.png', array('class' => 'permission-toggle', 'data-aco_id' => $id, 'data-aro_id' => $rolesAros[$roleId]));
					} else {
						$row[] = $this->Html->image('/img/icons/cross.png', array('class' => 'permission-toggle', 'data-aco_id' => $id, 'data-aro_id' => $rolesAros[$roleId]));
					}
				} else {
					$row[] = $this->Html->image('/img/icons/tick_disabled.png', array('class' => 'permission-disabled'));
				}
			} else {
				$row[] = '';
			}
		}

		echo $this->Html->tableCells(array($row), $oddOptions, $evenOptions);
	}

	echo $tableHeaders;
?>
</table>
