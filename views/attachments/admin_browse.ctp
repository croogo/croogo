<div class="attachments index">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Attachment', true), array('action'=>'add', 'editor' => 1)); ?></li>
		</ul>
	</div>

	<table cellpadding="0" cellspacing="0">
	<?php
		$tableHeaders = $this->Html->tableHeaders(array(
			$paginator->sort('id'),
			'&nbsp;',
			$paginator->sort('title'),
			'&nbsp;',
			__('URL', true),
			__('Actions', true),
		));
		echo $tableHeaders;

		$rows = array();
		foreach ($attachments AS $attachment) {
			$actions  = $this->Html->link(__('Edit', true), array(
				'controller' => 'attachments',
				'action' => 'edit',
				$attachment['Node']['id'],
			));
			$actions .= ' ' . $this->Html->link(__('Delete', true), array(
				'controller' => 'attachments',
				'action' => 'delete',
				$attachment['Node']['id'],
				'token' => $this->params['_Token']['key'],
			), null, __('Are you sure?', true));

			$mimeType = explode('/', $attachment['Node']['mime_type']);
			$mimeType = $mimeType['0'];
			if ($mimeType == 'image') {
				$thumbnail = $this->Html->link($this->Image->resize($attachment['Node']['path'], 100, 200), '#', array(
					'onclick' => "selectURL('".$attachment['Node']['slug']."');",
					'escape' => false,
				));
			} else {
				$thumbnail = $this->Html->image('/img/icons/page_white.png') . ' ' . $attachment['Node']['mime_type'] . ' (' . $this->Filemanager->filename2ext($attachment['Node']['slug']) . ')';
				$thumbnail = $this->Html->link($thumbnail, '#', array(
					'onclick' => "selectURL('".$attachment['Node']['slug']."');",
					'escape' => false,
				));
			}

			$insertCode = $this->Html->link(__('Insert', true), '#', array('onclick' => 'selectURL("'.$attachment['Node']['slug'].'");'));

			$rows[] = array(
				$attachment['Node']['id'],
				$thumbnail,
				$attachment['Node']['title'],
				$insertCode,
				$this->Html->link($this->Text->truncate(Router::url($attachment['Node']['path'], true), 20), $attachment['Node']['path']),
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
