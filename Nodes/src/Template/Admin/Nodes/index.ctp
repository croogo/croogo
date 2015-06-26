<?php
$this->extend('Croogo/Core./Common/admin_index');
use Croogo\Core\CroogoStatus;

$this->Croogo->adminScript(array('Croogo/Nodes.admin'));

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
		'url' => [
			'action' => 'process'
		],
		'class' => 'form-inline',
		'id' => 'NodeAction'
	)
);

?>
<div class="row-fluid">
	<div class="span12">
		<table class="table table-striped">
		<?php
			$tableHeaders = $this->Html->tableHeaders(array(
				$this->Form->checkbox('checkAll'),
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
					<td><?php echo $this->Form->checkbox('Nodes.' . $node->id . '.id', array('class' => 'row-select')); ?></td>
					<td><?php echo $node->id; ?></td>
					<td>
						<span>
						<?php
							echo $this->Html->link($node->title, array(
								'prefix' => false,
								'action' => 'view',
								'type' => $node->type,
								'slug' => $node->slug
							));
						?>
						</span>
						<?php if ($node->promote == 1): ?>
						<span class="label label-info"><?php echo __d('croogo', 'promoted'); ?></span>
						<?php endif ?>
						<?php if ($node->status == CroogoStatus::PREVIEW): ?>
						<span class="label label-warning"><?php echo __d('croogo', 'preview'); ?></span>
						<?php endif ?>
					</td>
					<td>
						<?php echo $node->type; ?>
					</td>
					<td>
						<?php echo $node->user->username; ?>
					</td>
					<td>
						<?php
							echo $this->element('Croogo/Core.admin/toggle', array(
								'id' => $node->id,
								'status' => (int)$node->status,
							));
						?>
					</td>
					<td>
						<div class="item-actions">
						<?php
							echo $this->Croogo->adminRowActions($node->id);
							echo ' ' . $this->Croogo->adminRowAction('',
								array('action' => 'edit', $node->id),
								array('icon' => 'pencil', 'tooltip' => __d('croogo', 'Edit this item'))
							);
							echo ' ' . $this->Croogo->adminRowAction('',
								'#Node' . $node->id . 'Id',
								array(
									'icon' => 'copy',
									'tooltip' => __d('croogo', 'Create a copy'),
									'rowAction' => 'copy',
								)
							);
							echo ' ' . $this->Croogo->adminRowAction('',
								'#Node' . $node->id . 'Id',
								array(
									'icon' => 'trash',
									'class' => 'delete',
									'tooltip' => __d('croogo', 'Remove this item'),
									'rowAction' => 'delete',
								),
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
				echo $this->Form->input('Nodes.action', array(
					'label' => __d('croogo', 'Applying to selected'),
					'div' => 'input inline',
					'options' => array(
						'publish' => __d('croogo', 'Publish'),
						'unpublish' => __d('croogo', 'Unpublish'),
						'promote' => __d('croogo', 'Promote'),
						'unpromote' => __d('croogo', 'Unpromote'),
						'delete' => __d('croogo', 'Delete'),
						'copy' => __d('croogo', 'Copy'),
					),
					'empty' => true,
				));
			?>
				<div class="controls">
				<?php
					$jsVarName = uniqid('confirmMessage_');
					echo $this->Form->button(__d('croogo', 'Submit'), array(
						'type' => 'button',
						'onclick' => sprintf('return Nodes.confirmProcess(%s)', $jsVarName),
					));
				?>
					<script>
						var <?php echo h($jsVarName); ?> = <?php echo json_encode(__d('croogo', '%s selected items?')); ?>;
					</script>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Form->end(); ?>
