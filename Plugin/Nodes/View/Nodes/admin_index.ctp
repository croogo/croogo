<?php
$this->extend('/Common/admin_index');
$this->Html->script(array('Nodes.nodes'), false);

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Content'), $this->here);

?>
<?php $this->start('actions'); ?>
	<li>
		<?php echo $this->Html->link(
			__('Create content'),
			array('action'=>'create'),
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
echo $this->element('admin/nodes_filter');

echo $this->Form->create(
	'Node',
	array(
		'url' => array('controller' => 'nodes', 'action' => 'process'),
		'class' => 'form-inline'
	)
);

?>
<div class="row-fluid">
	<div class="span12">
		<table class="table table-striped">
		<?php
			$tableHeaders =  $this->Html->tableHeaders(array(
				'',
				$this->Paginator->sort('id'),
				$this->Paginator->sort('title'),
				$this->Paginator->sort('type'),
				$this->Paginator->sort('user_id'),
				$this->Paginator->sort('status'),
				''
			));
		?>
			<thead>
				<?php echo $tableHeaders; ?>
			</thead>

			<tbody>
			<?php foreach ($nodes as $node): ?>
				<tr>
					<td><?php echo $this->Form->checkbox('Node.'.$node['Node']['id'].'.id'); ?></td>
					<td><?php echo $node['Node']['id']; ?></td>
					<td>
						<span>
						<?php
							echo $this->Html->link($node['Node']['title'], array(
								'admin' => false,
								'controller' => 'nodes',
								'action' => 'view',
								'type' => $node['Node']['type'],
								'slug' => $node['Node']['slug']
							));
						?>
						</span>
						<?php if ($node['Node']['promote']): ?>
						<span class="label label-info"><?php echo __('promoted'); ?></span>
						<?php endif ?>
					</td>
					<td>
						<?php echo $node['Node']['type']; ?>
					</td>
					<td>
						<?php echo $node['User']['username']; ?>
					</td>
					<td>
						<?php
							echo $this->element('admin/toggle', array(
								'id' => $node['Node']['id'],
								'status' => $node['Node']['status'],
							));
						?>
					</td>
					<td>
						<div class="item-actions">
						<?php
							echo $this->Croogo->adminRowActions($node['Node']['id']);
							echo ' ' . $this->Croogo->adminRowAction('',
								array('action' => 'edit', $node['Node']['id']),
								array('icon' => 'pencil', 'tooltip' => __('Edit this item'))
							);
							echo ' ' . $this->Croogo->adminRowAction('',
								'#Node' . $node['Node']['id'] . 'Id',
								array('icon' => 'trash', 'tooltip' => __('Remove this item'), 'rowAction' => 'delete'),
								__('Are you sure?')
							);
						?>
						</div>
					</td>
				</tr>
			<?php endforeach ?>
			</tbody>

		</table>

		<div class="row-fluid">
			<div id="bulk-action" class="control-group">
			<?php
				echo $this->Form->input('Node.action', array(
					'label' => __('Applying to selected'),
					'div' => 'input inline',
					'options' => array(
						'publish' => __('Publish'),
						'unpublish' => __('Unpublish'),
						'promote' => __('Promote'),
						'unpromote' => __('Unpromote'),
						'delete' => __('Delete'),
					),
					'empty' => true,
				));
			?>
				<div class="controls">
				<?php
					$jsVarName = uniqid('confirmMessage_');
					echo $this->Form->button(__('Submit'), array(
						'type' => 'button',
						'onclick' => sprintf('return Nodes.confirmProcess(app.%s)', $jsVarName),
					));
					$this->Js->set($jsVarName, __('%s selected items?'));
				?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Form->end(); ?>
