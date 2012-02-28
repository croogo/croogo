<div class="menus index">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Menu', true), array('action'=>'add')); ?></li>
		</ul>
	</div>

	<table cellpadding="0" cellspacing="0">
	<?php
		$tableHeaders =  $this->Html->tableHeaders(array(
			$paginator->sort('id'),
			$paginator->sort('title'),
			$paginator->sort('alias'),
			$paginator->sort('link_count'),
			__('Actions', true),
		));
		echo $tableHeaders;

		$rows = array();
		foreach ($menus AS $menu) {
			$actions  = $this->Html->link(__('View links', true), array('controller' => 'links', 'action' => 'index', $menu['Menu']['id']));
			$actions .= ' ' . $this->Html->link(__('Edit', true), array('controller' => 'menus', 'action' => 'edit', $menu['Menu']['id']));
			$actions .= ' ' . $this->Layout->adminRowActions($menu['Menu']['id']);
			$actions .= ' ' . $this->Html->link(__('Delete', true), array(
				'controller' => 'menus',
				'action' => 'delete',
				$menu['Menu']['id'],
				'token' => $this->params['_Token']['key'],
			), null, __('Are you sure?', true));

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

<div class="paging"><?php echo $paginator->numbers(); ?></div>
<div class="counter"><?php echo $paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true))); ?></div>
