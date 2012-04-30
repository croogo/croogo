<div class="users index">
	<h2><?php __('Users');?></h2>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New User', true), array('action'=>'add')); ?></li>
		</ul>
	</div>

	<table cellpadding="0" cellspacing="0">
	<?php
		$tableHeaders =  $this->Html->tableHeaders(array(
			$paginator->sort('id'),
			__('Role', true),
			$paginator->sort('username'),
			$paginator->sort('name'),
			$paginator->sort('email'),
			__('Actions', true),
		));
		echo $tableHeaders;

		$rows = array();
		foreach ($users AS $user) {
			$actions  = $this->Html->link(__('Edit', true), array('controller' => 'users', 'action' => 'edit', $user['User']['id']));
			$actions .= ' ' . $this->Layout->adminRowActions($user['User']['id']);
			$actions .= ' ' . $this->Html->link(__('Delete', true), array(
				'controller' => 'users',
				'action' => 'delete',
				$user['User']['id'],
				'token' => $this->params['_Token']['key'],
			), null, __('Are you sure?', true));

			$rows[] = array(
				$user['User']['id'],
				h($user['Role']['title']),
				h($user['User']['username']),
				h($user['User']['name']),
				$user['User']['email'],
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