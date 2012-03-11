<div class="contacts index">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Contact'), array('action'=>'add')); ?></li>
		</ul>
	</div>

	<table cellpadding="0" cellspacing="0">
	<?php
		$tableHeaders =  $this->Html->tableHeaders(array(
			$this->Paginator->sort('id'),
			$this->Paginator->sort('title'),
			$this->Paginator->sort('alias'),
			$this->Paginator->sort('email'),
			__('Actions'),
		));
		echo $tableHeaders;

		$rows = array();
		foreach ($contacts AS $contact) {
			$actions = '';
			//$actions .= $this->Html->link(__('View Messages'), array('controller'=> 'messages', 'action' => 'index', 'contact' => $contact['Contact']['id']));
			$actions .= ' ' . $this->Html->link(__('Edit'), array('action' => 'edit', $contact['Contact']['id']));
			$actions .= ' ' . $this->Layout->adminRowActions($contact['Contact']['id']);
			$actions .= ' ' . $this->Html->link(__('Delete'), array(
				'action' => 'delete',
				$contact['Contact']['id'],
				'token' => $this->params['_Token']['key'],
			), null, __('Are you sure?'));

			$rows[] = array(
				$contact['Contact']['id'],
				$this->Html->link($contact['Contact']['title'], array(
					'admin' => false,
					'controller' => 'contacts',
					'action' => 'view',
					$contact['Contact']['alias'],
				)),
				$contact['Contact']['alias'],
				$contact['Contact']['email'],
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
