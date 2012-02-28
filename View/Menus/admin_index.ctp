<div class="menus index">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Menu'), array('action'=>'add')); ?></li>
		</ul>
	</div>

	<table cellpadding="0" cellspacing="0">
	<?php
		$tableHeaders =  $this->Html->tableHeaders(array(
			$this->Paginator->sort('id'),
			$this->Paginator->sort('title'),
			$this->Paginator->sort('alias'),
			$this->Paginator->sort('link_count'),
			__('Actions'),
		));
		echo $tableHeaders;

		$rows = array();
		foreach ($menus AS $menu) {
			$actions  = $this->Html->link(__('View links'), array('controller' => 'links', 'action' => 'index', $menu['Menu']['id']));
			$actions .= ' ' . $this->Html->link(__('Edit'), array('controller' => 'menus', 'action' => 'edit', $menu['Menu']['id']));
			$actions .= ' ' . $this->Layout->adminRowActions($menu['Menu']['id']);
			$actions .= ' ' . $this->Html->link(__('Delete'), array(
				'controller' => 'menus',
				'action' => 'delete',
				$menu['Menu']['id'],
				'token' => $this->params['_Token']['key'],
			), null, __('Are you sure?'));

			$rows[] = array(
				$menu['Menu']['id'],
				$this->Html->link($menu['Menu']['title'], array('controller' => 'links', 'action' => 'index', $menu['Menu']['id'])),
				$menu['Menu']['alias'],
				$menu['Menu']['link_count'],
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
