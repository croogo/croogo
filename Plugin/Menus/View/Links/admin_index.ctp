<?php

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Menus'), array('plugin' => 'menus', 'controller' => 'menus', 'action' => 'index'))
	->addCrumb(__($menu['Menu']['title']), array(
		'plugin' => 'menus', 'controller' => 'links', 'action' => 'index',
		'?' => array('menu_id' => $menu['Menu']['id'])));
?>

<?php $this->start('actions'); ?>
<li>
	<?php
	echo $this->Html->link(
		__('New %s', Inflector::singularize($this->name)),
		array('action' => 'add', $menu['Menu']['id']),
		array('button' => 'default')
	);
	?>
</li>
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
		__('Id'),
		__('Title'),
		__('Status'),
		__('Actions'),
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
			'tooltip' => __('Move up'),
		));
		$actions[] = $this->Croogo->adminRowAction('', array(
			'controller' => 'links', 'action' => 'movedown', $linkId,
			), array(
			'icon' => 'chevron-down',
			'tooltip' => __('Move down'),
		));
		$actions[] = $this->Croogo->adminRowActions($linkId);
		$actions[] = $this->Croogo->adminRowAction('', array(
			'controller' => 'links', 'action' => 'edit', $linkId,
			), array(
			'icon' => 'pencil', 'tooltip' => __('Edit this item'),
		));
		$actions[] = $this->Croogo->adminRowAction('', '#Link' . $linkId . 'Id',
			array(
				'icon' => 'trash',
				'tooltip' => __('Delete this item'),
				'rowAction' => 'delete',
			),
			__('Are you sure?')
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
					'publish' => __('Publish'),
					'unpublish' => __('Unpublish'),
					'delete' => __('Delete'),
				),
				'empty' => true,
			));
		?>
		<div class="controls">
			<?php echo $this->Form->end(__('Submit')); ?>
		</div>
	</div>
</div>