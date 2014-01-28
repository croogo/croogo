<?php

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Menus'), '/' . $this->request->url);

?>
<div class="row-fluid">
	<div class="span12">
		<table class="table table-striped">
			<?php
				$tableHeaders = $this->Html->tableHeaders(array(
					$this->Paginator->sort('id', __d('croogo', 'Id')),
					$this->Paginator->sort('title', __d('croogo', 'Title')),
					$this->Paginator->sort('alias', __d('croogo', 'Alias')),
					$this->Paginator->sort('link_count', __d('croogo', 'Link Count')),
					$this->Paginator->sort('status', __d('croogo', 'Status')),
					__d('croogo', 'Actions'),
				));
			?>
			<thead>
				<?php echo $tableHeaders; ?>
			</thead>

			<?php
			$rows = array();
			foreach ($menus as $menu):
				$actions = array();
				$actions[] = $this->Croogo->adminRowAction(
					'',
					array('controller' => 'links', 'action' => 'index',	'?' => array('menu_id' => $menu['Menu']['id'])),
					array('icon' => 'zoom-in', 'tooltip' => __d('croogo', 'View links'))
				);
				$actions[] = $this->Croogo->adminRowActions($menu['Menu']['id']);
				$actions[] = $this->Croogo->adminRowAction(
					'',
					array('controller' => 'menus', 'action' => 'edit', $menu['Menu']['id']),
					array('icon' => 'pencil', 'tooltip' => __d('croogo', 'Edit this item'))
				);
				$actions[] = $this->Croogo->adminRowAction(
					'',
					array('controller' => 'menus', 'action' => 'delete', $menu['Menu']['id']),
					array('icon' => 'trash', 'tooltip' => __d('croogo', 'Remove this item')),
					__d('croogo', 'Are you sure?')
				);
				$actions = $this->Html->div('item-actions', implode(' ', $actions));

				$title = $this->Html->link($menu['Menu']['title'], array(
					'controller' => 'links',
					'?' => array(
						'menu_id' => $menu['Menu']['id']
					)
				));
				if ($menu['Menu']['status'] === CroogoStatus::PREVIEW) {
					$title .= ' ' . $this->Html->tag('span', __d('croogo', 'preview'),
						array('class' => 'label label-warning')
					);
				}

				$status = $this->element('admin/toggle', array(
					'id' => $menu['Menu']['id'],
					'status' => $menu['Menu']['status'],
				));

				$rows[] = array(
					$menu['Menu']['id'],
					$title,
					$menu['Menu']['alias'],
					$menu['Menu']['link_count'],
					$status,
					$this->Html->div('item-actions', $actions),
				);
			endforeach;

			echo $this->Html->tableCells($rows);
			?>
		</table>
	</div>
</div>
