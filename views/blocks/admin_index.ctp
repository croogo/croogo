<div class="blocks index">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Block', true), array('action'=>'add')); ?></li>
		</ul>
	</div>

	<?php echo $this->Form->create('Block', array('url' => array('controller' => 'blocks', 'action' => 'process'))); ?>
	<table cellpadding="0" cellspacing="0">
	<?php
		$tableHeaders = $this->Html->tableHeaders(array(
			'',
			$paginator->sort('id'),
			$paginator->sort('title'),
			$paginator->sort('alias'),
			$paginator->sort('region_id'),
			$paginator->sort('status'),
			__('Actions', true),
		));
		echo $tableHeaders;

		$rows = array();
		foreach ($blocks AS $block) {
			$actions  = $this->Html->link(__('Move up', true), array('controller' => 'blocks', 'action' => 'moveup', $block['Block']['id']));
			$actions .= ' ' . $this->Html->link(__('Move down', true), array('controller' => 'blocks', 'action' => 'movedown', $block['Block']['id']));
			$actions .= ' ' . $this->Html->link(__('Edit', true), array('controller' => 'blocks', 'action' => 'edit', $block['Block']['id']));
			$actions .= ' ' . $this->Layout->adminRowActions($block['Block']['id']);
			$actions .= ' ' . $this->Html->link(__('Delete', true), array(
				'controller' => 'blocks',
				'action' => 'delete',
				$block['Block']['id'],
				'token' => $this->params['_Token']['key'],
			), null, __('Are you sure?', true));

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
				'publish' => __('Publish', true),
				'unpublish' => __('Unpublish', true),
				'delete' => __('Delete', true),
			),
			'empty' => true,
		));
		echo $this->Form->end(__('Submit', true));
	?>
	</div>
</div>

<div class="paging"><?php echo $paginator->numbers(); ?></div>
<div class="counter"><?php echo $paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true))); ?></div>
