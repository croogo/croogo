<div class="users index">
	<h2><?php echo __('Users');?></h2>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New User'), array('action'=>'add')); ?></li>
		</ul>
	</div>

	<table cellpadding="0" cellspacing="0">
	<?php
		$tableHeaders =  $this->Html->tableHeaders(array(
			$this->Paginator->sort('id'),
			__('Role'),
			$this->Paginator->sort('username'),
			$this->Paginator->sort('name'),
			$this->Paginator->sort('email'),
			$this->Paginator->sort('status'),
			__('Actions'),
		));
		echo $tableHeaders;

		$rows = array();
		foreach ($users AS $user) {
			$actions  = $this->Html->link(__('Edit'), array('controller' => 'users', 'action' => 'edit', $user['User']['id']));
			$actions .= ' ' . $this->Layout->adminRowActions($user['User']['id']);
			$actions .= ' ' . $this->Form->postLink(__('Delete'), array(
				'controller' => 'users',
				'action' => 'delete',
				$user['User']['id'],
			), null, __('Are you sure?'));

			$rows[] = array(
				$user['User']['id'],
				$user['Role']['title'],
				$user['User']['username'],
				$user['User']['name'],
				$user['User']['email'],
				$this->Layout->status($user['User']['status']),
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
