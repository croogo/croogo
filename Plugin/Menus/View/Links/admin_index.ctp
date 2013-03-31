<?php

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Menus'), array('plugin' => 'menus', 'controller' => 'menus', 'action' => 'index'))
	->addCrumb(__d('croogo', $menu['Menu']['title']), array(
		'plugin' => 'menus', 'controller' => 'links', 'action' => 'index',
		'?' => array('menu_id' => $menu['Menu']['id'])));
?>

<?php $this->start('actions'); ?>
<?php
	echo $this->Croogo->adminAction(
		__d('croogo', 'New %s', Inflector::singularize($this->name)),
		array('action' => 'add', $menu['Menu']['id']),
		array('button' => 'success')
	);
?>
<?php $this->end('actions'); ?>

<?php
	if (isset($this->params['named'])) {
		foreach ($this->params['named'] as $nn => $nv) {
			$this->Paginator->options['url'][] = $nn . ':' . $nv;
		}
	}

	echo $this->Form->create('Link', array(
		'url' => array(
			'action' => 'process',
			$menu['Menu']['id'],
		),
	));
?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		'',
		__d('croogo', 'Id'),
		__d('croogo', 'Title'),
		__d('croogo', 'Status'),
		__d('croogo', 'Actions'),
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
	<?php
	$rows = array();
	foreach ($linksTree as $linkId => $linkTitle):
		$actions = array();
		$actions[] = $this->Croogo->adminRowAction('', array(
			'controller' => 'links', 'action' => 'moveup', $linkId
			), array(
			'icon' => 'chevron-up',
			'tooltip' => __d('croogo', 'Move up'),
		));
		$actions[] = $this->Croogo->adminRowAction('', array(
			'controller' => 'links', 'action' => 'movedown', $linkId,
			), array(
			'icon' => 'chevron-down',
			'tooltip' => __d('croogo', 'Move down'),
		));
		$actions[] = $this->Croogo->adminRowActions($linkId);
		$actions[] = $this->Croogo->adminRowAction('', array(
			'controller' => 'links', 'action' => 'edit', $linkId,
			), array(
			'icon' => 'pencil', 'tooltip' => __d('croogo', 'Edit this item'),
		));
		$actions[] = $this->Croogo->adminRowAction('', '#Link' . $linkId . 'Id',
			array(
				'icon' => 'trash',
				'tooltip' => __d('croogo', 'Delete this item'),
				'rowAction' => 'delete',
			),
			__d('croogo', 'Are you sure?')
		);
		$actions = $this->Html->div('item-actions', implode(' ', $actions));
		$rows[] = array(
			$this->Form->checkbox('Link.' . $linkId . '.id'),
			$linkId,
			$linkTitle,
			$this->element('admin/toggle', array(
				'id' => $linkId,
				'status' => $linksStatus[$linkId],
			)),
			$actions,
		);
	endforeach;

	echo $this->Html->tableCells($rows);
	?>

</table>
<div class="row-fluid">
	<div id="bulk-action" class="control-group">
		<?php
			echo $this->Form->input('Link.action', array(
				'div' => 'input inline',
				'label' => false,
				'options' => array(
					'publish' => __d('croogo', 'Publish'),
					'unpublish' => __d('croogo', 'Unpublish'),
					'delete' => __d('croogo', 'Delete'),
				),
				'empty' => true,
			));
		?>
		<div class="controls">
			<?php echo $this->Form->end(__d('croogo', 'Submit')); ?>
		</div>
	</div>
</div>