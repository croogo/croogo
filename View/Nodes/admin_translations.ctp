<div class="nodes index">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="actions">
		<ul>
			<li>
			<?php
				echo $this->Html->link(__('Translate in a new language'), array(
					'controller' => 'languages',
					'action'=>'select',
					'nodes',
					'translate',
					$node['Node']['id'],
				));
			?>
			</li>
		</ul>
	</div>

	<?php if (count($translations) > 0) { ?>
	<table cellpadding="0" cellspacing="0">
	<?php
		$tableHeaders =  $this->Html->tableHeaders(array(
			'',
			//__('Id'),
			__('Title'),
			__('Locale'),
			__('Actions'),
		));
		echo $tableHeaders;

		$rows = array();
		foreach ($translations AS $translation) {
			$actions  = $this->Html->link(__('Edit'), array('action' => 'translate', $id, 'locale' => $translation[$runtimeModelAlias]['locale']));
			$actions .= ' ' . $this->Html->link(__('Delete'), array('action' => 'delete_translation', $translation[$runtimeModelAlias]['locale'], $id), null, __('Are you sure?'));

			$rows[] = array(
				'',
				//$translation[$RuntimeModelAlias]['id'],
				$translation[$runtimeModelAlias]['content'],
				$translation[$runtimeModelAlias]['locale'],
				$actions,
			);
		}

		echo $this->Html->tableCells($rows);
		echo $tableHeaders;
	?>
	</table>
	<?php
		} else {
			echo '<p>' . __('No translations available.') . '</p>';
		}
	?>
</div>