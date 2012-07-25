<?php
$this->extend('/Common/admin_index');
$this->name = 'acos';
$this->Html->script('/acl/js/acl_permissions.js', false);
$this->Html->scriptBlock("$(document).ready(function(){ AclPermissions.documentReady(); });", array('inline' => false));
?>

<?php $this->start('tabs'); ?>
<li><?php echo $this->Html->link(__('New Action'), array('action'=>'add')); ?></li>
<li><?php echo $this->Html->link(__('Generate Actions'), array('action'=>'generate')); ?></li>
<li><?php echo $this->Form->postLink(__('Sync Actions'), array('controller' => 'acl_actions', 'action'=>'generate', 'permissions' => 1, 'sync' => 1)); ?></li>
<?php $this->end(); ?>

<table class="permission-table" cellpadding="0" cellspacing="0">
<?php
	$tableHeaders =  $this->Html->tableHeaders(array(
		__('Id'),
		__('Alias'),
		__('Actions'),
	));
	echo $tableHeaders;

	$currentController = '';
	foreach ($acos AS $acoIndex => $aco) {
		$id = $aco['Aco']['id'];
		$alias = $aco['Aco']['alias'];
		$class = '';
		if(substr($alias, 0, 1) == '_') {
			$level = 1;
			$class .= 'level-'.$level;
			$oddOptions = array('class' => 'hidden controller-'.$currentController);
			$evenOptions = array('class' => 'hidden controller-'.$currentController);
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

		$actions  = $this->Html->link(__('Edit'), array('action' => 'edit', $id));
		$actions .= ' ' . $this->Form->postLink(__('Delete'), array(
			'action' => 'delete',
			$id,
		), null, __('Are you sure?'));
		$actions .= ' ' . $this->Html->link(__('Move up'), array('action' => 'move', $id, 'up'));
		$actions .= ' ' . $this->Html->link(__('Move down'), array('action' => 'move', $id, 'down'));

		$row = array(
			$id,
			$this->Html->div(trim($class), $alias, array(
				'data-id' => $id,
				'data-alias' => $alias,
				'data-level' => $level,
			)),
			$actions,
		);

		echo $this->Html->tableCells(array($row), $oddOptions, $evenOptions);
	}
	echo $tableHeaders;
?>
</table>
