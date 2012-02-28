<div class="messages index">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('Unread', true), array('action'=>'index', 'filter' => 'status:0;')); ?></li>
			<li><?php echo $this->Html->link(__('Read', true), array('action'=>'index', 'filter' => 'status:1;')); ?></li>
		</ul>
	</div>

	<?php
		if (isset($this->params['named'])) {
			foreach ($this->params['named'] AS $nn => $nv) {
				$paginator->options['url'][] = $nn . ':' . $nv;
			}
		}
	?>

	<?php echo $this->Form->create('Message', array('url' => array('controller' => 'messages', 'action' => 'process'))); ?>
	<table cellpadding="0" cellspacing="0">
	<?php
		$tableHeaders =  $this->Html->tableHeaders(array(
			'',
			$paginator->sort('id'),
			$paginator->sort('contact_id'),
			$paginator->sort('name'),
			$paginator->sort('email'),
			$paginator->sort('title'),
			'',
			__('Actions', true),
		));
		echo $tableHeaders;

		$rows = array();
		foreach ($messages AS $message) {
			$actions  = $this->Html->link(__('Edit', true), array('action' => 'edit', $message['Message']['id']));
			$actions .= ' ' . $this->Layout->adminRowActions($message['Message']['id']);
			$actions .= ' ' . $this->Html->link(__('Delete', true), array(
				'action' => 'delete',
				$message['Message']['id'],
				'token' => $this->params['_Token']['key'],
			), null, __('Are you sure?', true));

			$rows[] = array(
				$this->Form->checkbox('Message.'.$message['Message']['id'].'.id'),
				$message['Message']['id'],
				$message['Contact']['title'],
				$message['Message']['name'],
				$message['Message']['email'],
				$message['Message']['title'],
				$this->Html->image('/img/icons/comment.png'),
				$actions,
			);
		}

		echo $this->Html->tableCells($rows);
		echo $tableHeaders;
	?>
	</table>
	<div class="bulk-actions">
	<?php
		echo $this->Form->input('Message.action', array(
			'label' => false,
			'options' => array(
				'read' => __('Mark as read', true),
				'unread' => __('Mark as unread', true),
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
