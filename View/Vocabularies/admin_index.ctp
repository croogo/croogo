<?php $this->extend('/Common/admin_index'); ?>

<table cellpadding="0" cellspacing="0">
<?php
	$tableHeaders =  $this->Html->tableHeaders(array(
		$this->Paginator->sort('id'),
		$this->Paginator->sort('title'),
		$this->Paginator->sort('alias'),
		__('Actions'),
	));
	echo $tableHeaders;

	$rows = array();
	foreach ($vocabularies AS $vocabulary) {
		$actions  = $this->Html->link(__('View terms'), array('controller' => 'terms', 'action' => 'index', $vocabulary['Vocabulary']['id']));
		$actions .= ' ' . $this->Html->link(__('Edit'), array('action' => 'edit', $vocabulary['Vocabulary']['id']));
		$actions .= ' ' . $this->Html->link(__('Move up'), array('action' => 'moveup', $vocabulary['Vocabulary']['id']));
		$actions .= ' ' . $this->Html->link(__('Move down'), array('action' => 'movedown', $vocabulary['Vocabulary']['id']));
		$actions .= ' ' . $this->Layout->adminRowActions($vocabulary['Vocabulary']['id']);
		$actions .= ' ' . $this->Form->postLink(__('Delete'), array(
			'controller' => 'vocabularies',
			'action' => 'delete',
			$vocabulary['Vocabulary']['id'],
		), null, __('Are you sure?'));

		$rows[] = array(
			$vocabulary['Vocabulary']['id'],
			$this->Html->link($vocabulary['Vocabulary']['title'], array('controller' => 'terms', 'action' => 'index', $vocabulary['Vocabulary']['id'])),
			$vocabulary['Vocabulary']['alias'],
			$actions,
		);
	}

	echo $this->Html->tableCells($rows);
	echo $tableHeaders;
?>
</table>
