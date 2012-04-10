<div class="translate index">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="actions">
		<ul>
			<li>
			<?php
				echo $this->Html->link(__('Translate in a new language'), array(
					'plugin' => null,
					'controller' => 'languages',
					'action'=>'select',
					$record[$modelAlias]['id'],
					$modelAlias,
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
			$actions  = $this->Html->link(__('Edit'), array(
				'action' => 'edit',
				$id,
				$modelAlias,
				'locale' => $translation[$runtimeModelAlias]['locale'],
			));
			$actions .= ' ' . $this->Form->postLink(__('Delete'), array(
				'action' => 'delete',
				$id,
				$modelAlias,
				$translation[$runtimeModelAlias]['locale'],
			), null, __('Are you sure?'));

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