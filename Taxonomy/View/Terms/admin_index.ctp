<?php

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Vocabularies'), array('plugin' => 'taxonomy', 'controller' => 'vocabularies', 'action' => 'index'))
	->addCrumb($vocabulary['Vocabulary']['title'], array('plugin' => 'taxonomy', 'controller' => 'terms', 'action' => 'index', $vocabulary['Vocabulary']['id']));
?>

<?php $this->start('actions'); ?>
<?php
	echo $this->Croogo->adminAction(
		__d('croogo', 'New Term'),
		array('action' => 'add', $vocabulary['Vocabulary']['id'])
	);
?>
<?php $this->end(); ?>

<?php
	if (isset($this->request->params['named'])) {
		foreach ($this->request->params['named'] as $nn => $nv) {
			$this->Paginator->options['url'][] = $nn . ':' . $nv;
		}
	}
?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		'',
		__d('croogo', 'Id'),
		__d('croogo', 'Title'),
		__d('croogo', 'Slug'),
		__d('croogo', 'Actions'),
	));
?>
<thead>
	<?php echo $tableHeaders; ?>
</thead>
<?php
	$rows = array();

	foreach ($terms as $term):
		$actions = array();
		$actions[] = $this->Croogo->adminRowActions($term['Term']['id']);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'moveup',	$term['Term']['id'], $vocabulary['Vocabulary']['id']),
			array('icon' => 'chevron-up', 'tooltip' => __d('croogo', 'Move up'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'movedown', $term['Term']['id'], $vocabulary['Vocabulary']['id']),
			array('icon' => 'chevron-down', 'tooltip' => __d('croogo', 'Move down'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'edit', $term['Term']['id'], $vocabulary['Vocabulary']['id']),
			array('icon' => 'pencil', 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'delete', $term['Term']['id'], $vocabulary['Vocabulary']['id']),
			array('icon' => 'trash', 'tooltip' => __d('croogo', 'Remove this item')),
			__d('croogo', 'Are you sure?'));
		$actions = $this->Html->div('item-actions', implode(' ', $actions));

		// Title Column
		$titleCol = $term['Term']['title'];
		if (isset($defaultType['alias'])) {
			$titleCol = $this->Html->link($term['Term']['title'], array(
				'plugin' => 'nodes',
				'controller' => 'nodes',
				'action' => 'term',
				'type' => $defaultType['alias'],
				'slug' => $term['Term']['slug'],
				'admin' => 0,
			), array(
				'target' => '_blank',
			));
		}

		// Build link list
		$typeLinks = $this->Taxonomies->generateTypeLinks($vocabulary['Type'], $term);
		if (!empty($typeLinks)) {
			$titleCol .= $this->Html->tag('small', $typeLinks);
		}

		$rows[] = array(
			'',
			$term['Term']['id'],
			$titleCol,
			$term['Term']['slug'],
			$actions,
		);
	endforeach;

	echo $this->Html->tableCells($rows);

?>
</table>
