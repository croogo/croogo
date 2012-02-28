<div class="terms index">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Term'), array('action' => 'add', $vocabulary['Vocabulary']['id'])); ?></li>
		</ul>
	</div>

	<?php
		if (isset($this->params['named'])) {
			foreach ($this->params['named'] AS $nn => $nv) {
				$this->Paginator->options['url'][] = $nn . ':' . $nv;
			}
		}

		echo $this->Form->create('Term', array(
			'url' => array(
				'controller' => 'terms',
				'action' => 'process',
				'vocabulary' => $vocabulary['Vocabulary']['id'],
			),
		));
	?>
	<table cellpadding="0" cellspacing="0">
	<?php
		$tableHeaders =  $this->Html->tableHeaders(array(
			'',
			__('Id'),
			__('Title'),
			__('Slug'),
			__('Actions'),
		));
		echo $tableHeaders;

		$rows = array();
		foreach ($termsTree AS $id => $title) {
			$actions  = $this->Html->link(__('Move up'), array(
				'action' => 'moveup',
				$id,
				$vocabulary['Vocabulary']['id'],
			));
			$actions .= ' ' . $this->Html->link(__('Move down'), array(
				'action' => 'movedown',
				$id,
				$vocabulary['Vocabulary']['id'],
			));
			$actions .= ' ' . $this->Html->link(__('Edit'), array(
				'action' => 'edit',
				$id,
				$vocabulary['Vocabulary']['id'],
			));
			$actions .= ' ' . $this->Layout->adminRowActions($id);
			$actions .= ' ' . $this->Html->link(__('Delete'), array(
				'action' => 'delete',
				$id,
				$vocabulary['Vocabulary']['id'],
				'token' => $this->params['_Token']['key'],
			), null, __('Are you sure?'));

			$rows[] = array(
				'',
				$id,
				$title,
				$terms[$id]['slug'],
				$actions,
			);
		}

		echo $this->Html->tableCells($rows);
		echo $tableHeaders;
	?>
	</table>
</div>