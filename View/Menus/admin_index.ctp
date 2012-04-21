<?php $this->extend('/Common/admin_index'); ?>

<table cellpadding="0" cellspacing="0">
<?php
	$tableHeaders =  $this->Html->tableHeaders(array(
		$this->Paginator->sort('id'),
		$this->Paginator->sort('title'),
		$this->Paginator->sort('alias'),
		$this->Paginator->sort('link_count'),
		__('Actions'),
	));
	echo $tableHeaders;

	$rows = array();
	foreach ($menus AS $menu) {
		$actions  = $this->Html->link(__('View links'), array('controller' => 'links', 'action' => 'index', $menu['Menu']['id']));
		$actions .= ' ' . $this->Html->link(__('Edit'), array('controller' => 'menus', 'action' => 'edit', $menu['Menu']['id']));
		$actions .= ' ' . $this->Layout->adminRowActions($menu['Menu']['id']);
		$actions .= ' ' . $this->Form->postLink(__('Delete'), array(
			'controller' => 'menus',
			'action' => 'delete',
			$menu['Menu']['id'],
		), null, __('Are you sure?'));

		$rows[] = array(
			$menu['Menu']['id'],
			$this->Html->link($menu['Menu']['title'], array('controller' => 'links', 'action' => 'index', $menu['Menu']['id'])),
			$menu['Menu']['alias'],
			$menu['Menu']['link_count'],
			$actions,
		);
	}

	echo $this->Html->tableCells($rows);
	echo $tableHeaders;
?>
</table>
