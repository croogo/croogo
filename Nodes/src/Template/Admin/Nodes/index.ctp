<?php

use Cake\Utility\Hash;
use Croogo\Core\Status;

$this->extend('Croogo/Core./Common/admin_index');

$this->Croogo->adminScript('Croogo/Nodes.admin');

$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Content'), '/' . $this->request->url);

$this->append('actions');
	echo $this->Croogo->adminAction(
		__d('croogo', 'Create content'),
		array('action' => 'create'),
		array('button' => 'success')
	);
$this->end();

$this->append('search', $this->element('admin/nodes_search'));

$this->append('form-start', $this->CroogoForm->create(
	'Nodes',
	array(
		'url' => array('action' => 'process'),
		'class' => 'form-inline'
	)
));

$this->start('table-heading');
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->CroogoForm->checkbox('checkAll', ['id' => 'NodesCheckAll']),
		$this->Paginator->sort('id', __d('croogo', 'Id')),
		$this->Paginator->sort('title', __d('croogo', 'Title')),
		$this->Paginator->sort('type', __d('croogo', 'Type')),
		$this->Paginator->sort('user_id', __d('croogo', 'User')),
		$this->Paginator->sort('status', __d('croogo', 'Status')),
		''
	));
	echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->append('table-body');
?>
<tbody>
<?php foreach ($nodes as $node): ?>
	<tr>
		<td><?php echo $this->CroogoForm->checkbox('Nodes.' . $node->id . '.id', array('class' => 'row-select')); ?></td>
		<td><?php echo $node->id; ?></td>
		<td>
			<span>
			<?php
				echo $this->Html->link($node->title, Hash::merge(
					$node->url->getArrayCopy(),
					[
						'prefix' => false
					]
				));
			?>
			</span>

			<?php if ($node->promoted == 1): ?>
			<span class="label label-info"><?php echo __d('croogo', 'promoted'); ?></span>
			<?php endif ?>

			<?php if ($node->status == Status::PREVIEW): ?>
			<span class="label label-warning"><?php echo __d('croogo', 'preview'); ?></span>
			<?php endif ?>
		</td>
		<td>
		<?php
			echo $this->Html->link($node->type, array(
				'action' => 'hierarchy',
				'?' => array(
					'type' => $node->type,
				),
			));
		?>
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
					array('icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item'))
				);
				echo ' ' . $this->Croogo->adminRowAction('',
					'#Nodes' . $node->id . 'Id',
					array(
						'icon' => $this->Theme->getIcon('copy'),
						'tooltip' => __d('croogo', 'Create a copy'),
						'rowAction' => 'copy',
					)
				);
				echo ' ' . $this->Croogo->adminRowAction('',
					'#Nodes' . $node->id . 'Id',
					array(
						'icon' => $this->Theme->getIcon('delete'),
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
<?php
$this->end();

$this->start('bulk-action');
	echo $this->CroogoForm->input('Nodes.action', array(
		'label' => __d('croogo', 'Applying to selected'),
		'div' => 'input inline',
		'options' => array(
			'publish' => __d('croogo', 'Publish'),
			'unpublish' => __d('croogo', 'Unpublish'),
			'promote' => __d('croogo', 'Promote'),
			'unpromote' => __d('croogo', 'Unpromote'),
			'delete' => __d('croogo', 'Delete'),
			array(
				'value' => 'copy',
				'text' => __d('croogo', 'Copy'),
				'hidden' => true,
			),
		),
		'empty' => true,
	));

	$jsVarName = uniqid('confirmMessage_');
	$button = $this->CroogoForm->button(__d('croogo', 'Submit'), array(
		'type' => 'button',
		'class' => 'bulk-process',
		'data-relatedElement' => '#nodes-action',
		'data-confirmMessage' => $jsVarName,
		'escape' => true,
	));
	echo $this->Html->div('controls', $button);
	$this->Js->set($jsVarName, __d('croogo', '%s selected items?'));
	$this->Js->buffer("$('.bulk-process').on('click', Nodes.confirmProcess);");

$this->end();

$this->append('form-end', $this->CroogoForm->end());
