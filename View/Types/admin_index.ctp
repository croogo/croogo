<div class="types index">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Type'), array('action'=>'add')); ?></li>
		</ul>
	</div>

	<table cellpadding="0" cellspacing="0">
	<?php
		$tableHeaders =  $this->Html->tableHeaders(array(
			$this->Paginator->sort('id'),
			$this->Paginator->sort('title'),
			$this->Paginator->sort('alias'),
			$this->Paginator->sort('description'),
			__('Actions'),
		));
		echo $tableHeaders;

		$rows = array();
		foreach ($types AS $type) {
			$actions  = $this->Html->link(__('Edit'), array('controller' => 'types', 'action' => 'edit', $type['Type']['id']));
			$actions .= ' ' . $this->Layout->adminRowActions($type['Type']['id']);
			$actions .= ' ' . $this->Form->postLink(__('Delete'), array(
				'controller' => 'types',
				'action' => 'delete',
				$type['Type']['id'],
			), null, __('Are you sure?'));

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

<div class="paging"><?php echo $this->Paginator->numbers(); ?></div>
<div class="counter"><?php echo $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'))); ?></div>
