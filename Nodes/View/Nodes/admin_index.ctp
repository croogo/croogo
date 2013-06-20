<?php
$this->extend('/Common/admin_index');
$this->Html->script(array('Nodes.nodes'), false);

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Content'), '/' . $this->request->url);

?>
<?php $this->start('actions'); ?>
<?php
	echo $this->Croogo->adminAction(
		__d('croogo', 'Create content'),
		array('action' => 'create'),
		array('button' => 'success')
	);
?>
<?php $this->end(); ?>
<?php

echo $this->element('admin/nodes_search');

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
			$tableHeaders = $this->Html->tableHeaders(array(
				'',
				$this->Paginator->sort('id', __d('croogo', 'Id')),
				$this->Paginator->sort('title', __d('croogo', 'Title')),
				$this->Paginator->sort('type', __d('croogo', 'Type')),
				$this->Paginator->sort('user_id', __d('croogo', 'User')),
				$this->Paginator->sort('status', __d('croogo', 'Status')),
				''
			));
		?>
			<thead>
				<?php echo $tableHeaders; ?>
			</thead>

			<tbody>
			<?php foreach ($nodes as $node): ?>
				<tr>
					<td><?php echo $this->Form->checkbox('Node.' . $node['Node']['id'] . '.id'); ?></td>
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
						<span class="label label-info"><?php echo __d('croogo', 'promoted'); ?></span>
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
								array('icon' => 'pencil', 'tooltip' => __d('croogo', 'Edit this item'))
							);
							echo ' ' . $this->Croogo->adminRowAction('',
								'#Node' . $node['Node']['id'] . 'Id',
								array('icon' => 'trash', 'tooltip' => __d('croogo', 'Remove this item'), 'rowAction' => 'delete'),
								__d('croogo', 'Are you sure?')
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
					'label' => __d('croogo', 'Applying to selected'),
					'div' => 'input inline',
					'options' => array(
						'publish' => __d('croogo', 'Publish'),
						'unpublish' => __d('croogo', 'Unpublish'),
						'promote' => __d('croogo', 'Promote'),
						'unpromote' => __d('croogo', 'Unpromote'),
						'delete' => __d('croogo', 'Delete'),
					),
					'empty' => true,
				));
			?>
				<div class="controls">
				<?php
					$jsVarName = uniqid('confirmMessage_');
					echo $this->Form->button(__d('croogo', 'Submit'), array(
						'type' => 'button',
						'onclick' => sprintf('return Nodes.confirmProcess(app.%s)', $jsVarName),
					));
					$this->Js->set($jsVarName, __d('croogo', '%s selected items?'));
				?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Form->end(); ?>
