<div class="attachments index">

	<h2><?php echo $title_for_layout; ?></h2>

	<div class="row-fluid">
		<div class="span12 actions">
			<ul class="nav-buttons">
				<li><?php echo $this->Html->link(__('New Attachment'), array('action' => 'add', 'editor' => 1), array('button' => 'btn')); ?></li>
			</ul>
		</div>
	</div>

	<table class="table table-striped">
	<?php
		$tableHeaders = $this->Html->tableHeaders(array(
			$this->Paginator->sort('id'),
			'&nbsp;',
			$this->Paginator->sort('title'),
			'&nbsp;',
			__('URL'),
			__('Actions'),
		));
		echo $tableHeaders;

		$rows = array();
		foreach ($attachments as $attachment):
			$actions = array();
			$actions[] = $this->Croogo->adminRowAction('',
				array('controller' => 'attachments', 'action' => 'edit', $attachment['Node']['id']),
				array('icon' => 'pencil', 'tooltip' => __('Edit'))
			);
			$actions[] = $this->Croogo->adminRowAction('', array(
				'controller' => 'attachments',
				'action' => 'delete',
				$attachment['Node']['id'],
			), array('icon' => 'trash', 'tooltip' => __('Delete')), __('Are you sure?'));

			$mimeType = explode('/', $attachment['Node']['mime_type']);
			$mimeType = $mimeType['0'];
			if ($mimeType == 'image') {
				$thumbnail = $this->Html->link($this->Image->resize($attachment['Node']['path'], 100, 200), $attachment['Node']['path'], array(
					'class' => 'thickbox',
					'escape' => false,
					'title' => $attachment['Node']['title'],
				));
			} else {
				$thumbnail = $this->Html->image('/img/icons/page_white.png') . ' ' . $attachment['Node']['mime_type'] . ' (' . $this->Filemanager->filename2ext($attachment['Node']['slug']) . ')';
				$thumbnail = $this->Html->link($thumbnail, '#', array(
					'escape' => false,
				));
			}

			$actions = $this->Html->div('item-actions', implode(' ', $actions));

			$insertCode = $this->Html->link('', '#', array(
				'onclick' => "selectURL('" . $attachment['Node']['slug'] . "');",
				'icon' => 'paper-clip',
				'tooltip' => __('Insert')
			));

			$rows[] = array(
				$attachment['Node']['id'],
				$thumbnail,
				$attachment['Node']['title'],
				$insertCode,
				$this->Html->link(Router::url($attachment['Node']['path']),
					$attachment['Node']['path'],
					array('onclick' => "selectURL('" . $attachment['Node']['slug'] . "');")
				),
				$actions,
			);
		endforeach;

		echo $this->Html->tableCells($rows);
		echo $tableHeaders;
	?>
	</table>
</div>

<div class="row-fluid">
	<div class="span12">
		<div class="pagination">
		<ul>
			<?php echo $this->Paginator->first('< ' . __('first')); ?>
			<?php echo $this->Paginator->prev('< ' . __('prev')); ?>
			<?php echo $this->Paginator->numbers(); ?>
			<?php echo $this->Paginator->next(__('next') . ' >'); ?>
			<?php echo $this->Paginator->last(__('last') . ' >'); ?>
		</ul>
		</div>
		<div class="counter"><?php echo $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'))); ?></div>
	</div>
</div>
