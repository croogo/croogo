<?php

$this->extend('Croogo/Croogo./Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Content'), array('plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Vocabularies'), '/' . $this->request->url);

?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Paginator->sort('id', __d('croogo', 'Id')),
		$this->Paginator->sort('title', __d('croogo', 'Title')),
		$this->Paginator->sort('alias', __d('croogo', 'Alias')),
		$this->Paginator->sort('plugin', __d('croogo', 'Plugin')),
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
			array('controller' => 'Terms', 'action' => 'index', $vocabulary->id),
			array('icon' => 'zoom-in', 'tooltip' => __d('croogo', 'View terms'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'moveup', $vocabulary->id),
			array('icon' => 'chevron-up', 'tooltip' => __d('croogo', 'Move up'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'movedown', $vocabulary->id),
			array('icon' => 'chevron-down', 'tooltip' => __d('croogo', 'Move down'))
		);
		$actions[] = $this->Croogo->adminRowActions($vocabulary->id);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'edit', $vocabulary->id),
			array('icon' => 'pencil', 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'delete', $vocabulary->id),
			array('icon' => 'trash', 'tooltip' => __d('croogo', 'Remove this item')),
			__d('croogo', 'Are you sure?'));
		$actions = $this->Html->div('item-actions', implode(' ', $actions));
		$rows[] = array(
			$vocabulary->id,
			$this->Html->link($vocabulary->title, array('controller' => 'Terms', 'action' => 'index', $vocabulary->id)),
			$vocabulary->alias,
			$vocabulary->plugin,
			$actions,
		);
	endforeach;

	echo $this->Html->tableCells($rows);
?>
</table>
