<?php

if (empty($modelClass)) {
	$modelClass = Inflector::singularize($this->name);
}
if (!isset($className)) {
	$className = strtolower($this->name);
}
$humanName = Inflector::humanize(Inflector::underscore($modelClass));
$i18nDomain = empty($this->params['plugin']) ? 'croogo' : $this->params['plugin'];

$rowClass = $this->Theme->getCssClass('row');
$columnFull = $this->Theme->getCssClass('columnFull');
$tableClass = isset($tableClass) ? $tableClass : $this->Theme->getCssClass('tableClass');

$showActions = isset($showActions) ? $showActions : true;

if ($pageHeading = trim($this->fetch('page-heading'))):
	echo $pageHeading;
endif;
?>

<h2 class="hidden-desktop">
	<?php if ($titleBlock = $this->fetch('title')): ?>
		<?php echo $titleBlock; ?>
	<?php else: ?>
		<?php
		echo !empty($title_for_layout) ? $title_for_layout : $this->name;
		?>
	<?php endif; ?>
</h2>

<?php if ($showActions): ?>
<div class="<?php echo $rowClass; ?>">
	<div class="actions <?php echo $columnFull; ?>">
		<?php
		if ($actionsBlock = $this->fetch('actions')):
			echo $actionsBlock;
		else:
			echo $this->Croogo->adminAction(
				__d('croogo', 'New %s', __d($i18nDomain, $humanName)),
				array('action' => 'add'),
				array('button' => 'success')
			);
		endif;
		?>
	</div>
</div>
<?php endif; ?>

<?php
$tableHeaders = trim($this->fetch('table-heading'));
if (!$tableHeaders && isset($displayFields)):
	$tableHeaders = array();
	foreach ($displayFields as $field => $arr):
		if ($arr['sort']):
			$tableHeaders[] = $this->Paginator->sort($field, __d($i18nDomain, $arr['label']));
		else:
			$tableHeaders[] = __d($i18nDomain, $arr['label']);
		endif;
	endforeach;
	$tableHeaders[] = __d('croogo', 'Actions');
	$tableHeaders = $this->Html->tableHeaders($tableHeaders);
endif;

$tableBody = trim($this->fetch('table-body'));
if (!$tableBody && isset($displayFields)):
	$rows = array();
	if (!empty(${strtolower($this->name)})):
		foreach (${strtolower($this->name)} as $item):
			$actions = array();

			if (isset($this->request->query['chooser'])):
				$title = isset($item[$modelClass]['title']) ? $item[$modelClass]['title'] : null;
				$actions[] = $this->Croogo->adminRowAction(__d('croogo', 'Choose'), '#', array(
					'class' => 'item-choose',
					'data-chooser_type' => $modelClass,
					'data-chooser_id' => $item[$modelClass]['id'],
				));
			else:
				$actions[] = $this->Croogo->adminRowAction('',
					array('action' => 'edit', $item[$modelClass]['id']),
					array('icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item'))
				);
				$actions[] = $this->Croogo->adminRowActions($item[$modelClass]['id']);
				$actions[] = $this->Croogo->adminRowAction('',
					array(
						'action' => 'delete',
						$item[$modelClass]['id'],
					),
					array(
						'icon' => $this->Theme->getIcon('delete'),
						'tooltip' => __d('croogo', 'Remove this item')
					),
					__d('croogo', 'Are you sure?'));
			endif;
			$actions = $this->Html->div('item-actions', implode(' ', $actions));
			$row = array();
			foreach ($displayFields as $key => $val):
				extract($val);
				if (!is_int($key)) {
					$val = $key;
				}
				if (strpos($val, '.') === false) {
					$val = $modelClass . '.' . $val;
				}
				list($model, $field) = pluginSplit($val);
				$row[] = $this->Layout->displayField($item, $model, $field, compact('type', 'url', 'options'));
			endforeach;
			$row[] = $actions;
			$rows[] = $row;
		endforeach;
		$tableBody = $this->Html->tableCells($rows);
	endif;
endif;

$tableFooters = trim($this->fetch('table-footer'));

?>
<div class="<?php echo $rowClass; ?>">
	<div class="<?php echo $columnFull; ?>">
	<?php
		$searchBlock = $this->fetch('search');
		if (!$searchBlock):
			$searchBlock = $this->element('admin/search');
		endif;
		echo $searchBlock;

		if ($contentBlock = trim($this->fetch('content'))):
			echo $this->element('admin/search');
			echo $contentBlock;
		else:

			if ($formStart = trim($this->fetch('form-start'))):
				echo $formStart;
			endif;

			if ($mainBlock = trim($this->fetch('main'))):
				echo $mainBlock;
			else:
			?>
			<table class="<?php echo $tableClass; ?>">
			<?php
				echo $tableHeaders;
				echo $tableBody;
				if ($tableFooters):
					echo $tableFooters;
				endif;
			?>
			</table>
			<?php endif; ?>

			<?php if ($bulkAction = trim($this->fetch('bulk-action'))): ?>
			<div class="<?php echo $rowClass; ?>">
				<div id="bulk-action" class="control-group">
					<?php echo $bulkAction; ?>
				</div>
			</div>
			<?php endif; ?>

			<?php
			if ($formEnd = trim($this->fetch('form-end'))):
				echo $formEnd;
			endif;
			?>

		<?php endif; ?>
	</div>
</div>

<div class="<?php echo $rowClass; ?>">
	<div class="<?php echo $columnFull; ?>">
		<?php
		if ($pagingBlock = $this->fetch('paging')):
			echo $pagingBlock;
		else:
			if (isset($this->Paginator) && isset($this->request['paging'])):
				echo $this->element('admin/pagination');
			endif;
		endif;
		?>
	</div>
</div>
<?php

if ($pageFooter = trim($this->fetch('page-footer'))):
	echo $pageFooter;
endif;
