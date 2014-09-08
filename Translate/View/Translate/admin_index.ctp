<?php

$this->extend('/Common/admin_index');
$this->name = 'translate';


$plugin = $controller = 'nodes';
if (isset($this->request->params['models'][$modelAlias])) {
	$plugin = $this->request->params['models'][$modelAlias]['plugin'];
	$controller = strtolower(Inflector::pluralize($modelAlias));
}
$this->Html
	->addCrumb('', '/admin', array('icon' => $_icons['home']))
	->addCrumb(
		Inflector::pluralize($modelAlias),
		array(
			'plugin' => Inflector::underscore($plugin),
			'controller' => Inflector::underscore($controller),
			'action' => 'index',
		)
	)
	->addCrumb(
		$record[$modelAlias][$displayField],
		array(
			'plugin' => Inflector::underscore($plugin),
			'controller' =>  Inflector::underscore($controller),
			'action' => 'edit',
			$record[$modelAlias]['id'],
		)
	)
	->addCrumb(__d('croogo', 'Translations'), '/' . $this->request->url);

?>
<?php $this->start('actions'); ?>
<?php
	echo $this->Croogo->adminAction(
		__d('croogo', 'Translate in a new language'),
		array(
			'plugin' => 'settings',
			'controller' => 'languages',
			'action' => 'select',
			$record[$modelAlias]['id'],
			$modelAlias
		),
		array(
			'button' => 'default',
		)
	);
?>
<?php $this->end(); ?>

<?php if (count($translations) > 0): ?>
	<table class="table table-striped">
	<?php
		$tableHeaders = $this->Html->tableHeaders(array(
			'',
			__d('croogo', 'Title'),
			__d('croogo', 'Locale'),
			__d('croogo', 'Actions'),
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
				'icon' => $_icons['update'],
				'tooltip' => __d('croogo', 'Edit this item'),
			));
			$actions[] = $this->Croogo->adminRowAction('', array(
				'action' => 'delete',
				$id,
				$modelAlias,
				$translation[$runtimeModelAlias]['locale'],
			), array(
				'icon' => $_icons['delete'],
				'tooltip' => __d('croogo', 'Remove this item'),
			), __d('croogo', 'Are you sure?'));

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
	<p><?php echo __d('croogo', 'No translations available.'); ?></p>
<?php endif; ?>
