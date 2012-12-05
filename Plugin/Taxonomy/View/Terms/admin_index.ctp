<?php

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'))
	->addCrumb(__('Vocabularies'), array('plugin' => 'taxonomy', 'controller' => 'vocabularies', 'action' => 'index'))
	->addCrumb($vocabulary['Vocabulary']['title'], array('plugin' => 'taxonomy', 'controller' => 'terms', 'action' => 'index', $vocabulary['Vocabulary']['id']));
?>

<?php $this->start('actions'); ?>
	<li>
		<?php echo $this->Html->link(
			__('New Term'),
			array('action' => 'add', $vocabulary['Vocabulary']['id']),
			array('button' => 'default')
		); ?>
	</li>
<?php $this->end(); ?>

<?php
	if (isset($this->params['named'])) {
		foreach ($this->params['named'] as $nn => $nv) {
			$this->Paginator->options['url'][] = $nn . ':' . $nv;
		}
	}

	echo $this->Form->create('Term', array(
		'url' => array(
			'controller' => 'terms',
			'action' => 'process',
			'vocabulary' => $vocabulary['Vocabulary']['id'],
		),
	));
?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		'',
		__('Id'),
		__('Title'),
		__('Slug'),
		__('Actions'),
	));
?>
<thead>
	<?php echo $tableHeaders; ?>
</thead>
<?php	
	$rows = array();
	foreach ($termsTree as $id => $title):
		$actions = array();
		$actions[] = $this->Croogo->adminRowActions($id);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'moveup',	$id, $vocabulary['Vocabulary']['id']),
			array('icon' => 'chevron-up', 'tooltip' => __('Move up'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'movedown', $id, $vocabulary['Vocabulary']['id']),
			array('icon' => 'chevron-down', 'tooltip' => __('Move down'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'edit', $id, $vocabulary['Vocabulary']['id']),
			array('icon' => 'pencil', 'tooltip' => __('Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'delete',	$id, $vocabulary['Vocabulary']['id']),
			array('icon' => 'trash', 'tooltip' => __('Remove this item')),
			__('Are you sure?'));
		$actions = $this->Html->div('item-actions', implode(' ', $actions));
		$rows[] = array(
			'',
			$id,
			$title,
			$terms[$id]['slug'],
			$actions,
		);
	endforeach;

	echo $this->Html->tableCells($rows);

?>
</table>
