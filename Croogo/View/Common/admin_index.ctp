<?php
if (empty($modelClass)) {
	$modelClass = Inflector::singularize($this->name);
}
if (!isset($className)) {
	$className = strtolower($this->name);
}

$showActions = isset($showActions) ? $showActions : true;
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
<div class="<?php echo $this->Layout->cssClass('row'); ?>">
	<div class="actions <?php echo $this->Layout->cssClass('columnFull'); ?>">
		<?php if ($actionsBlock = $this->fetch('actions')): ?>
			<?php echo $actionsBlock; ?>
		<?php else: ?>
		<?php
			echo $this->Croogo->adminAction(
				__d('croogo', 'New %s', __d('croogo', Inflector::singularize($this->name))),
				array('action' => 'add'),
				array('button' => 'success')
			);
		?>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>

<div class="<?php echo $this->Layout->cssClass('row'); ?>">
	<div class="<?php echo $this->Layout->cssClass('columnFull'); ?>">
	<?php
		$searchBlock = $this->fetch('search');
		if (!$searchBlock):
			$searchBlock = $this->element('admin/search');
		endif;
		echo $searchBlock;

		if ($contentBlock = $this->fetch('content')):
			echo $this->element('admin/search');
			echo $contentBlock;
		else:

			if ($formStart = trim($this->fetch('form-start'))):
				echo $formStart;
			endif;

		?>
			<table class="table table-striped">
			<?php
				$tableHeaders = trim($this->fetch('table-heading'));
				if (!$tableHeaders):
					$tableHeaders = array();
					foreach ($displayFields as $field => $arr):
						if ($arr['sort']):
							$tableHeaders[] = $this->Paginator->sort($field, __d('croogo', $arr['label']));
						else:
							$tableHeaders[] = __d('croogo', $arr['label']);
						endif;
					endforeach;
					$tableHeaders[] = __d('croogo', 'Actions');
					$tableHeaders = $this->Html->tableHeaders($tableHeaders);
				endif;
				echo $tableHeaders;

				$tableBody = trim($this->fetch('table-body'));
				if (!$tableBody):
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
									array('icon' => 'pencil', 'tooltip' => __d('croogo', 'Edit this item'))
								);
								$actions[] = $this->Croogo->adminRowActions($item[$modelClass]['id']);
								$actions[] = $this->Croogo->adminRowAction('',
									array(
										'action' => 'delete',
										$item[$modelClass]['id'],
									),
									array(
										'icon' => 'trash',
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
				echo $tableBody;

				if ($tableFooters = trim($this->fetch('table-footer'))):
					echo $tableFooters;
				endif;
			?>
			</table>

			<?php if ($bulkAction = trim($this->fetch('bulk-action'))): ?>
			<div class="<?php echo $this->Layout->cssClass('row'); ?>">
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

<div class="<?php echo $this->Layout->cssClass('row'); ?>">
	<div class="<?php echo $this->Layout->cssClass('columnFull'); ?>">
		<?php if ($pagingBlock = $this->fetch('paging')): ?>
			<?php echo $pagingBlock; ?>
		<?php else: ?>
			<?php if (isset($this->Paginator) && isset($this->request['paging'])): ?>
				<?php echo $this->element('admin/pagination'); ?>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
