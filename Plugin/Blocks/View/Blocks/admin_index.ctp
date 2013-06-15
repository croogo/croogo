<?php

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Blocks'), array('action' => 'index'));

echo $this->Form->create('Block',
	array('url' => array('controller' => 'blocks', 'action' => 'process')),
	array('class' => 'form-inline')
);

$chooser = isset($this->request->query['chooser']);

?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		'',
		$this->Paginator->sort('id', __d('croogo', 'Id')),
		$this->Paginator->sort('title', __d('croogo', 'Title')),
		$this->Paginator->sort('alias', __d('croogo', 'Alias')),
		$this->Paginator->sort('region_id', __d('croogo', 'Region')),
		$this->Paginator->sort('status', __d('croogo', 'Status')),
		__d('croogo', 'Actions'),
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
			array('icon' => 'arrow-up', 'tooltip' => __d('croogo', 'Move up'),
		));
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'blocks', 'action' => 'movedown', $block['Block']['id']),
			array('icon' => 'arrow-down', 'tooltip' => __d('croogo', 'Move down'),
			)
		);
		$actions[] = $this->Croogo->adminRowActions($block['Block']['id']);
		$actions[] = $this->Croogo->adminRowAction('',
			array('controller' => 'blocks', 'action' => 'edit', $block['Block']['id']),
			array('icon' => 'pencil', 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			'#Block' . $block['Block']['id'] . 'Id',
			array('icon' => 'trash', 'tooltip' => __d('croogo', 'Remove this item'), 'rowAction' => 'delete'),
			__d('croogo', 'Are you sure?')
		);

		if ($chooser) {
			$checkbox = null;
			$actions = array(
				$this->Croogo->adminRowAction(__d('croogo', 'Choose'), '#', array(
					'class' => 'item-choose',
					'data-chooser_type' => 'Block',
					'data-chooser_id' => $block['Block']['id'],
					'data-chooser_title' => $block['Block']['title'],
				)),
			);
		} else {
			$checkbox = $this->Form->checkbox('Block.' . $block['Block']['id'] . '.id');
		}

		$actions = $this->Html->div('item-actions', implode(' ', $actions));

		$rows[] = array(
			$checkbox,
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

<?php if (!$chooser): ?>
<div class="row-fluid">
	<div id="bulk-action" class="control-group">
		<?php
			echo $this->Form->input('Block.action', array(
				'label' => false,
				'div' => 'input inline',
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
<?php endif; ?>
