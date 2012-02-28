<div class="types index">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Type', true), array('action'=>'add')); ?></li>
		</ul>
	</div>

	<table cellpadding="0" cellspacing="0">
	<?php
		$tableHeaders =  $this->Html->tableHeaders(array(
			$paginator->sort('id'),
			$paginator->sort('title'),
			$paginator->sort('alias'),
			$paginator->sort('description'),
			__('Actions', true),
		));
		echo $tableHeaders;

		$rows = array();
		foreach ($types AS $type) {
			$actions  = $this->Html->link(__('Edit', true), array('controller' => 'types', 'action' => 'edit', $type['Type']['id']));
			$actions .= ' ' . $this->Layout->adminRowActions($type['Type']['id']);
			$actions .= ' ' . $this->Html->link(__('Delete', true), array(
				'controller' => 'types',
				'action' => 'delete',
				$type['Type']['id'],
				'token' => $this->params['_Token']['key'],
			), null, __('Are you sure?', true));

			$rows[] = array(
				$type['Type']['id'],
				$type['Type']['title'],
				$type['Type']['alias'],
				$this->Text->truncate($type['Type']['description'], 50),
				$actions,
			);
		}

		echo $this->Html->tableCells($rows);
		echo $tableHeaders;
	?>
	</table>
</div>

<div class="paging"><?php echo $paginator->numbers(); ?></div>
<div class="counter"><?php echo $paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true))); ?></div>
