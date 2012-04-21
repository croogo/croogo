<?php
$this->extend('/Common/admin_index');
$this->Html->script(array('nodes'), false);
?>
<?php $this->start('tabs'); ?>
	<li><?php echo $this->Html->link(__('Create content'), array('action'=>'create')); ?></li>
<?php $this->end(); ?>

<?php
if (isset($this->params['named'])) {
	foreach ($this->params['named'] AS $nn => $nv) {
		$this->Paginator->options['url'][] = $nn . ':' . $nv;
	}
}
echo $this->element('admin/nodes_filter');
?>

<?php echo $this->Form->create('Node', array('url' => array('controller' => 'nodes', 'action' => 'process'))); ?>
<table cellpadding="0" cellspacing="0">
<?php
	$tableHeaders =  $this->Html->tableHeaders(array(
		'',
		$this->Paginator->sort('id'),
		$this->Paginator->sort('title'),
		$this->Paginator->sort('type'),
		$this->Paginator->sort('user_id'),
		$this->Paginator->sort('status'),
		$this->Paginator->sort('promote'),
		//$this->Paginator->sort('created'),
		__('Actions'),
	));
	echo $tableHeaders;

	$rows = array();
	foreach ($nodes AS $node) {
		$actions  = $this->Html->link(__('Edit'), array('action' => 'edit', $node['Node']['id']));
		$actions .= ' ' . $this->Layout->adminRowActions($node['Node']['id']);
		$actions .= ' ' . $this->Layout->processLink(__('Delete'),
			'#Node' . $node['Node']['id'] . 'Id',
			null, __('Are you sure?'));

		$rows[] = array(
			$this->Form->checkbox('Node.'.$node['Node']['id'].'.id'),
			$node['Node']['id'],
			$this->Html->link($node['Node']['title'], array(
				'admin' => false,
				'controller' => 'nodes',
				'action' => 'view',
				'type' => $node['Node']['type'],
				'slug' => $node['Node']['slug'],
			)),
			$node['Node']['type'],
			$node['User']['username'],
			$this->Layout->status($node['Node']['status']),
			$this->Layout->status($node['Node']['promote']),
			//$node['Node']['created'],
			$actions,
		);
	}

	echo $this->Html->tableCells($rows);
	echo $tableHeaders;
?>
</table>

<div class="bulk-actions">
<?php
	echo $this->Form->input('Node.action', array(
		'label' => false,
		'options' => array(
			'publish' => __('Publish'),
			'unpublish' => __('Unpublish'),
			'promote' => __('Promote'),
			'unpromote' => __('Unpromote'),
			'delete' => __('Delete'),
		),
		'empty' => true,
	));
	$jsVarName = uniqid('confirmMessage_');
	echo $this->Form->button(__('Submit'), array(
		'type' => 'button',
		'onclick' => sprintf('return Nodes.confirmProcess(app.%s)', $jsVarName),
		));
	$this->Js->set($jsVarName, __('%s selected items?'));

	echo $this->Form->end();
?>
</div>
