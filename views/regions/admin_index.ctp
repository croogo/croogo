<div class="regions index">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Region', true), array('action'=>'add')); ?></li>
		</ul>
	</div>

	<table cellpadding="0" cellspacing="0">
	<?php
		$tableHeaders =  $this->Html->tableHeaders(array(
			$paginator->sort('id'),
			$paginator->sort('title'),
			$paginator->sort('alias'),
			__('Actions', true),
		));
		echo $tableHeaders;

		$rows = array();
		foreach ($regions AS $region) {
			$actions  = $this->Html->link(__('Edit', true), array('controller' => 'regions', 'action' => 'edit', $region['Region']['id']));
			$actions .= ' ' . $this->Layout->adminRowActions($region['Region']['id']);
			$actions .= ' ' . $this->Html->link(__('Delete', true), array(
				'controller' => 'regions',
				'action' => 'delete',
				$region['Region']['id'],
				'token' => $this->params['_Token']['key'],
			), null, __('Are you sure?', true));

			$rows[] = array(
				$region['Region']['id'],
				$region['Region']['title'],
				$region['Region']['alias'],
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
