<?php

$this->extend('/Common/admin_index');

$this->Html->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Settings'), array('plugin' => 'settings', 'controller' => 'settings', 'action' => 'prefix', 'Site'))
	->addCrumb(__('Languages'), $this->here);

?>
<div class="row-fluid">
	<div class="span12">
		<table class="table table-striped">
			<?php
				$tableHeaders = $this->Html->tableHeaders(array(
					$this->Paginator->sort('id'),
					$this->Paginator->sort('title'),
					$this->Paginator->sort('native'),
					$this->Paginator->sort('alias'),
					$this->Paginator->sort('status'),
					__('Actions'),
				));
				?>
					<thead>
						<?php echo $tableHeaders; ?>
					</thead>
				<?php

				$rows = array();
				foreach ($languages as $language) {
					$actions = array();
					$actions[] = $this->Croogo->adminRowActions($language['Language']['id']);
					$actions[] = $this->Croogo->adminRowAction('',
						array('action' => 'moveup', $language['Language']['id']),
						array('icon' => 'chevron-up', 'tooltip' => __('Move up'))
					);
					$actions[] = $this->Croogo->adminRowAction('',
						array('action' => 'movedown', $language['Language']['id']),
						array('icon' => 'chevron-down', 'tooltip' => __('Move down'))
					);
					$actions[] = $this->Croogo->adminRowAction('',
						array('action' => 'edit', $language['Language']['id']),
						array('icon' => 'pencil', 'tooltip' => __('Edit this item'))
					);
					$actions[] = $this->Croogo->adminRowAction('',
						array('action' => 'delete', $language['Language']['id']),
						array('icon' => 'trash', 'tooltip' => __('Remove this item')),
						__('Are you sure?')
					);

					$actions = $this->Html->div('item-actions', implode(' ', $actions));

					$rows[] = array(
						$language['Language']['id'],
						$language['Language']['title'],
						$language['Language']['native'],
						$language['Language']['alias'],
						$this->Html->status($language['Language']['status']),
						$actions,
					);
				}

				echo $this->Html->tableCells($rows);
			?>
			</table>
		</div>
	</div>
</div>
