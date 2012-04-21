<div class="extensions-locales">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('Upload'), array('action'=>'add')); ?></li>
		</ul>
	</div>

	<table cellpadding="0" cellspacing="0">
	<?php
		$tableHeaders =  $this->Html->tableHeaders(array(
			'',
			__('Locale'),
			__('Default'),
			__('Actions'),
		));
		echo $tableHeaders;

		$rows = array();
		foreach ($locales AS $locale) {
			$actions  = '';
			$actions .= $this->Form->postLink(__('Activate'), array(
				'action' => 'activate',
				$locale,
			));
			$actions .= ' ' . $this->Html->link(__('Edit'), array('action' => 'edit', $locale));
			$actions .= ' ' . $this->Form->postLink(__('Delete'), array(
				'action' => 'delete',
				$locale,
			), null, __('Are you sure?'));

			if ($locale == Configure::read('Site.locale')) {
				$status = $this->Layout->status(1);
			} else {
				$status = $this->Layout->status(0);
			}

			$rows[] = array(
				'',
				$locale,
				$status,
				$actions,
			);
		}

		echo $this->Html->tableCells($rows);
		echo $tableHeaders;
	?>
	</table>
</div>