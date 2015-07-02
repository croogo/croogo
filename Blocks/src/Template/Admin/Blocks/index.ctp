<?php

use Croogo\Core\Status;

$this->Croogo->adminScript('Croogo/Blocks.admin');

$this->extend('Croogo/Core./Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Blocks'), array('action' => 'index'));

$this->append('form-start', $this->Form->create('Block', array(
	'url' => array('action' => 'process'),
)));

$chooser = isset($this->request->query['chooser']);
$this->start('table-heading');
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Form->checkbox('checkAll'),
		$this->Paginator->sort('id', __d('croogo', 'Id')),
		$this->Paginator->sort('title', __d('croogo', 'Title')),
		$this->Paginator->sort('alias', __d('croogo', 'Alias')),
		$this->Paginator->sort('region_id', __d('croogo', 'Region')),
		$this->Paginator->sort('status', __d('croogo', 'Status')),
		__d('croogo', 'Actions'),
	));
	echo $this->Html->tag('thead',$tableHeaders);
$this->end();

$this->append('table-body');
	$rows = array();
	foreach ($blocks as $block) {
		$actions = array();
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'moveup', $block->id),
			array('icon' => $this->Theme->getIcon('move-up'), 'tooltip' => __d('croogo', 'Move up'),
		));
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'movedown', $block->id),
			array('icon' => $this->Theme->getIcon('move-down'), 'tooltip' => __d('croogo', 'Move down'),
			)
		);
		$actions[] = $this->Croogo->adminRowActions($block->id);
		$actions[] = $this->Croogo->adminRowAction('',
			array('action' => 'edit', $block->id),
			array('icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item'))
		);
		$actions[] = $this->Croogo->adminRowAction('',
			'#Block' . $block->id . 'Id',
			array(
				'icon' => $this->Theme->getIcon('copy'),
				'tooltip' => __d('croogo', 'Create a copy'),
				'rowAction' => 'copy',
			),
			__d('croogo', 'Create a copy of this Block?')
		);
		$actions[] = $this->Croogo->adminRowAction('',
			'#Block' . $block->id . 'Id',
			array('icon' => $this->Theme->getIcon('delete'), 'class' => 'delete', 'tooltip' => __d('croogo', 'Remove this item'), 'rowAction' => 'delete'),
			__d('croogo', 'Are you sure?')
		);

		if ($chooser) {
			$checkbox = null;
			$actions = array(
				$this->Croogo->adminRowAction(__d('croogo', 'Choose'), '#', array(
					'class' => 'item-choose',
					'data-chooser_type' => 'Block',
					'data-chooser_id' => $block->id,
					'data-chooser_title' => $block->title,
				)),
			);
		} else {
			$checkbox = $this->Form->checkbox('Block.' . $block->id . '.id', array('class' => 'row-select'));
		}

		$actions = $this->Html->div('item-actions', implode(' ', $actions));
		$title = $this->Html->link($block->title, array(
			'action' => 'edit',
			$block->id,
		));

		if ($block->status == Status::PREVIEW) {
			$title .= ' ' . $this->Html->tag('span', __d('croogo', 'preview'),
				array('class' => 'label label-warning')
			);
		}

		$rows[] = array(
			$checkbox,
			$block->id,
			$title,
			$block->alias,
			$block->region->title,
			$this->element('Croogo/Core.admin/toggle', array(
				'id' => $block['Block']['id'],
				'status' => (int)$block->status,
			)),
			$actions,
		);
	}

	echo $this->Html->tableCells($rows);
?>
</table>
<?php
$this->end();
if (!$chooser):
	$this->start('bulk-action');
	echo $this->Form->input('Block.action', array(
		'label' => false,
		'div' => 'input inline',
		'options' => array(
			'publish' => __d('croogo', 'Publish'),
			'unpublish' => __d('croogo', 'Unpublish'),
			'delete' => __d('croogo', 'Delete'),
			'copy' => __d('croogo', 'Copy'),
		),
		'empty' => true,
	));
	$button = $this->Form->button(__d('croogo', 'Submit'), array(
		'type' => 'submit',
		'value' => 'submit'
	));
	echo $this->Html->div('controls', $button);
endif;
$this->end();
$this->append('form-end', $this->Form->end());
