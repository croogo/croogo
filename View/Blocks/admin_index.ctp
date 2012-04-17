<?php $this->extend('/Common/admin_index'); ?>

<?php echo $this->Form->create('Block', array('url' => array('controller' => 'blocks', 'action' => 'process'))); ?>
<table cellpadding="0" cellspacing="0">
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
	echo $tableHeaders;

	$rows = array();
	foreach ($blocks AS $block) {
		$actions  = $this->Html->link(__('Move up'), array('controller' => 'blocks', 'action' => 'moveup', $block['Block']['id']));
		$actions .= ' ' . $this->Html->link(__('Move down'), array('controller' => 'blocks', 'action' => 'movedown', $block['Block']['id']));
		$actions .= ' ' . $this->Html->link(__('Edit'), array('controller' => 'blocks', 'action' => 'edit', $block['Block']['id']));
		$actions .= ' ' . $this->Layout->adminRowActions($block['Block']['id']);
		$actions .= ' ' . $this->Layout->processLink(__('Delete'),
			'#Block' . $block['Block']['id'] .'Id',
			null, __('Are you sure?'));

		$rows[] = array(
			$this->Form->checkbox('Block.'.$block['Block']['id'].'.id'),
			$block['Block']['id'],
			$this->Html->link($block['Block']['title'], array('controller' => 'blocks', 'action' => 'edit', $block['Block']['id'])),
			$block['Block']['alias'],
			$block['Region']['title'],
			$this->Layout->status($block['Block']['status']),
			$actions,
		);
	}

	echo $this->Html->tableCells($rows);
	echo $tableHeaders;
?>
</table>
<div class="bulk-actions">
<?php
	echo $this->Form->input('Block.action', array(
		'label' => false,
		'options' => array(
			'publish' => __('Publish'),
			'unpublish' => __('Unpublish'),
			'delete' => __('Delete'),
		),
		'empty' => true,
	));
	echo $this->Form->end(__('Submit'));
?>
</div>
