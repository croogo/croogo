<?php
$this->extend('/Common/admin_index');
$this->name = 'translate';


$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Translate'), $this->here)
	->addCrumb($modelAlias)
	->addCrumb($record[$modelAlias]['title'], array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'edit', $record[$modelAlias]['id']));

?>
<?php $this->start('actions'); ?>
<li><?php
echo $this->Html->link(
	__('Translate in a new language'),
	array(
		'plugin' => 'settings',
		'controller' => 'languages',
		'action'=>'select',
		$record[$modelAlias]['id'],
		$modelAlias
	),
	array('button' => 'default')
);
?></li>
<?php $this->end(); ?>

<?php if (count($translations) > 0): ?>
	<table class="table table-striped">
	<?php
		$tableHeaders = $this->Html->tableHeaders(array(
			'',
			//__('Id'),
			__('Title'),
			__('Locale'),
			__('Actions'),
		));
	?>
		<thead>
			<?php echo $tableHeaders; ?>
		</thead>
	<?php
		$rows = array();
		foreach ($translations as $translation):
			$actions = array();
			$actions[] = $this->Croogo->adminRowAction('', array(
				'action' => 'edit',
				$id,
				$modelAlias,
				'locale' => $translation[$runtimeModelAlias]['locale'],
			), array(
				'icon' => 'pencil',
				'tooltip' => __('Edit this item'),
			));
			$actions[] = $this->Croogo->adminRowAction('', array(
				'action' => 'delete',
				$id,
				$modelAlias,
				$translation[$runtimeModelAlias]['locale'],
			), array(
				'icon' => 'trash',
				'tooltip' => __('Remove this item'),
			) , __('Are you sure?'));

			$actions = $this->Html->div('item-actions', implode(' ', $actions));
			$rows[] = array(
				'',
				$translation[$runtimeModelAlias]['content'],
				$translation[$runtimeModelAlias]['locale'],
				$actions,
			);
		endforeach;

		echo $this->Html->tableCells($rows);
	?>
	</table>
<?php else: ?>
	<p><?php echo __('No translations available.'); ?></p>
<?php endif; ?>
