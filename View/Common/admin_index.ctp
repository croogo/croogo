<?php
if (empty($modelClass)) {
	$modelClass = Inflector::singularize($this->name);
}
if (!isset($className)) {
	$className = strtolower($this->name);
}
?>

<h2 class="hidden-desktop">
	<?php if ($titleBlock = $this->fetch('title')): ?>
		<?php echo $titleBlock; ?>
	<?php else: ?>
		<?php
		echo!empty($title_for_layout) ? $title_for_layout : $this->name;
		?>
	<?php endif; ?>
</h2>

<div class="row-fluid">
	<div class="span12 actions">
		<ul class="nav-buttons">
			<?php if ($actionsBlock = $this->fetch('actions')): ?>
				<?php echo $actionsBlock; ?>
			<?php else: ?>
			<li>
				<?php
				echo $this->Html->link(__('New %s', Inflector::singularize($this->name)),
					array('action' => 'add'),
					array('class' => 'btn')
				);
				?>
			</li>
			<?php endif; ?>
		</ul>
	</div>
</div>

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
						$tableHeaders[] = $this->Paginator->sort($field, $arr['label']);
					} else {
						$tableHeaders[] = $arr['label'];
					}
				}
				$tableHeaders[] = __('Actions');
				$tableHeaders = $this->Html->tableHeaders($tableHeaders);

				$rows = array();
				if (!empty(${strtolower($this->name)})) {
					foreach (${strtolower($this->name)} as $item):
						$actions = array();
						$actions[] = $this->Croogo->adminRowAction(
							'',
							array('action' => 'edit', $item[$modelClass]['id']),
							array('icon' => 'pencil', 'tooltip' => __('Edit this item'))
						);
						$actions[] = $this->Croogo->adminRowActions($item[$modelClass]['id']);
						$actions[] = $this->Croogo->adminRowAction(
								'',
								array(
									'action' => 'delete',
									$item[$modelClass]['id'],
								),
								array(
									'icon' => 'trash',
									'tooltip' => __('Remove this item')
								),
								__('Are you sure?'));
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
					<ul>
						<?php echo $this->Paginator->first('< ' . __('first')); ?>
						<?php echo $this->Paginator->prev('< ' . __('prev')); ?>
						<?php echo $this->Paginator->numbers(); ?>
						<?php echo $this->Paginator->next(__('next') . ' >'); ?>
						<?php echo $this->Paginator->last(__('last') . ' >'); ?>
					</ul>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
