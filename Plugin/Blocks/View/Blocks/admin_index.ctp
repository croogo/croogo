<?php

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Blocks'), array('action' => 'index'));

echo $this->Form->create('Block',
	array('url' => array('controller' => 'blocks', 'action' => 'process')),
	array('class' => 'form-inline')
);

?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		'',
		$this->Paginator->sort('id'),
		$this->Paginator->sort('title'),
		$this->Paginator->sort('alias'),
		$this->Paginator->sort('region_id'),
		$this->Paginator->sort('status'),
		__('Actions'),
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
<?php
	$rows = array();
	foreach ($blocks as $block) {
		$actions = array();
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'blocks', 'action' => 'moveup', $block['Block']['id']),
			array('icon' => 'arrow-up', 'tooltip' => __('Move up'),
		));
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'blocks', 'action' => 'movedown', $block['Block']['id']),
			array('icon' => 'arrow-down', 'tooltip' => __('Move down'),
			)
		);
		$actions[] = $this->Croogo->adminRowActions($block['Block']['id']);
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'blocks', 'action' => 'edit', $block['Block']['id']),
			array('icon' => 'pencil', 'tooltip' => __('Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			'#Block' . $block['Block']['id'] . 'Id',
			array('icon' => 'trash', 'tooltip' => __('Remove this item'), 'rowAction' => 'delete'),
			__('Are you sure?')
		);

		$actions = $this->Html->div('item-actions', implode(' ', $actions));

		$rows[] = array(
			$this->Form->checkbox('Block.' . $block['Block']['id'] . '.id'),
			$block['Block']['id'],
			$this->Html->link($block['Block']['title'], array('controller' => 'blocks', 'action' => 'edit', $block['Block']['id'])),
			$block['Block']['alias'],
			$block['Region']['title'],
			$this->element('admin/toggle', array(
				'id' => $block['Block']['id'],
				'status' => $block['Block']['status'],
			)),
			$actions,
		);
	}

	echo $this->Html->tableCells($rows);
?>
</table>
<div class="row-fluid">
	<div id="bulk-action" class="control-group">
		<?php
			echo $this->Form->input('Block.action', array(
				'label' => false,
				'div' => 'input inline',
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
