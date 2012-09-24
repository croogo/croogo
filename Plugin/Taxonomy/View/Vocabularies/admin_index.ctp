<?php

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'))
	->addCrumb(__('Vocabularies'), $this->here);

?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Paginator->sort('id'),
		$this->Paginator->sort('title'),
		$this->Paginator->sort('alias'),
		__('Actions'),
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
<?php

	$rows = array();
	foreach ($vocabularies as $vocabulary) :
		$actions = array();
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'terms', 'action' => 'index', $vocabulary['Vocabulary']['id']),
			array('icon' => 'zoom-in', 'tooltip' => __('View terms'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'moveup', $vocabulary['Vocabulary']['id']),
			array('icon' => 'chevron-up', 'tooltip' => __('Move up'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'movedown', $vocabulary['Vocabulary']['id']),
			array('icon' => 'chevron-down', 'tooltip' => __('Move down'))
		);
		$actions[] = $this->Croogo->adminRowActions($vocabulary['Vocabulary']['id']);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'edit', $vocabulary['Vocabulary']['id']),
			array('icon' => 'pencil', 'tooltip' => __('Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'vocabularies', 'action' => 'delete', $vocabulary['Vocabulary']['id']),
			array('icon' => 'trash', 'tooltip' => __('Remove this item')),
			__('Are you sure?'));
		$actions = $this->Html->div('item-actions', implode(' ', $actions));
		$rows[] = array(
			$vocabulary['Vocabulary']['id'],
			$this->Html->link($vocabulary['Vocabulary']['title'], array('controller' => 'terms', 'action' => 'index', $vocabulary['Vocabulary']['id'])),
			$vocabulary['Vocabulary']['alias'],
			$actions,
		);
	endforeach;

	echo $this->Html->tableCells($rows);
?>
</table>
