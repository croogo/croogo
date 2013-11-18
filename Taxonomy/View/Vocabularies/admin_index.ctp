<?php

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Vocabularies'), '/' . $this->request->url);

?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Paginator->sort('id', __d('croogo', 'Id')),
		$this->Paginator->sort('title', __d('croogo', 'Title')),
		$this->Paginator->sort('alias', __d('croogo', 'Alias')),
		__d('croogo', 'Actions'),
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
			array('icon' => 'zoom-in', 'tooltip' => __d('croogo', 'View terms'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'moveup', $vocabulary['Vocabulary']['id']),
			array('icon' => 'chevron-up', 'tooltip' => __d('croogo', 'Move up'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'movedown', $vocabulary['Vocabulary']['id']),
			array('icon' => 'chevron-down', 'tooltip' => __d('croogo', 'Move down'))
		);
		$actions[] = $this->Croogo->adminRowActions($vocabulary['Vocabulary']['id']);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'edit', $vocabulary['Vocabulary']['id']),
			array('icon' => 'pencil', 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'vocabularies', 'action' => 'delete', $vocabulary['Vocabulary']['id']),
			array('icon' => 'trash', 'tooltip' => __d('croogo', 'Remove this item')),
			__d('croogo', 'Are you sure?'));
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
