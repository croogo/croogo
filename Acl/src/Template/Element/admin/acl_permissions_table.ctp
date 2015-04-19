<table class="table permission-table">
<?php
	$roleTitles = array_values($roles);
	$roleIds = array_keys($roles);

	$tableHeaders = array(
		__d('croogo', 'Id'),
		__d('croogo', 'Alias'),
	);
	$tableHeaders = array_merge($tableHeaders, $roleTitles);
	$tableHeaders = $this->Html->tableHeaders($tableHeaders);
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
<?php

	$icon = '<i class="pull-right"></i>';
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
			$this->Html->div(trim($class), $alias . $icon, array(
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
