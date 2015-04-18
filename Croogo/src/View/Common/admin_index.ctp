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
<div class="row-fluid">
	<div class="span12 actions">
		<ul class="nav-buttons">
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
		</ul>
	</div>
</div>
<?php endif; ?>

<div class="row-fluid">
	<div class="span12">
		<?php if ($contentBlock = $this->fetch('content')): ?>
			<?php echo $this->element('admin/search'); ?>
			<?php echo $contentBlock; ?>
		<?php else: ?>
			<?php echo $this->element('admin/search'); ?>
			<table class="table table-striped">
				<?php
				$tableHeaders = array();
				foreach ($displayFields as $field => $arr) {
					if ($arr['sort']) {
						$tableHeaders[] = $this->Paginator->sort($field, __d('croogo', $arr['label']));
					} else {
						$tableHeaders[] = __d('croogo', $arr['label']);
					}
				}
				$tableHeaders[] = __d('croogo', 'Actions');
				$tableHeaders = $this->Html->tableHeaders($tableHeaders);

				$rows = array();
				if (!empty(${strtolower($this->name)})) {
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
						foreach ($displayFields as $key => $val) {
							extract($val);
							if (!is_int($key)) {
								$val = $key;
							}
							if (strpos($val, '.') === false) {
								$val = $modelClass . '.' . $val;
							}
							list($model, $field) = pluginSplit($val);
							$row[] = $this->Layout->displayField($item, $model, $field, compact('type', 'url', 'options'));
						}
						$row[] = $actions;
						$rows[] = $row;
					endforeach;
				}
				?>
				<?php echo $this->Html->tableCells($rows); ?>
				<thead>
					<?php echo $tableHeaders; ?>
				</thead>
			</table>
		<?php endif; ?>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<?php if ($pagingBlock = $this->fetch('paging')): ?>
			<?php echo $pagingBlock; ?>
		<?php else: ?>
			<?php if (isset($this->Paginator) && isset($this->request['paging'])): ?>
				<div class="pagination">
					<p>
					<?php
					echo $this->Paginator->counter(array(
						'format' => __d('croogo', 'Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
					));
					?>
					</p>
					<ul>
						<?php echo $this->Paginator->first('< ' . __d('croogo', 'first')); ?>
						<?php echo $this->Paginator->prev('< ' . __d('croogo', 'prev')); ?>
						<?php echo $this->Paginator->numbers(); ?>
						<?php echo $this->Paginator->next(__d('croogo', 'next') . ' >'); ?>
						<?php echo $this->Paginator->last(__d('croogo', 'last') . ' >'); ?>
					</ul>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
