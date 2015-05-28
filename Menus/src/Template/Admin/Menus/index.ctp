<?php

use Croogo\Croogo\CroogoStatus;

$this->extend('Croogo/Croogo./Common/admin_index');

$this->CroogoHtml
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Menus'), '/' . $this->request->url);

?>
<div class="row-fluid">
	<div class="span12">
		<table class="table table-striped">
			<?php
				$tableHeaders = $this->CroogoHtml->tableHeaders(array(
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
					array('controller' => 'Links', 'action' => 'index',	'?' => array('menu_id' => $menu->id)),
					array('icon' => 'zoom-in', 'tooltip' => __d('croogo', 'View links'))
				);
				$actions[] = $this->Croogo->adminRowActions($menu->id);
				$actions[] = $this->Croogo->adminRowAction(
					'',
					array('action' => 'edit', $menu->id),
					array('icon' => 'pencil', 'tooltip' => __d('croogo', 'Edit this item'))
				);
				$actions[] = $this->Croogo->adminRowAction(
					'',
					array('action' => 'delete', $menu->id),
					array('icon' => 'trash', 'tooltip' => __d('croogo', 'Remove this item')),
					__d('croogo', 'Are you sure?')
				);
				$actions = $this->CroogoHtml->div('item-actions', implode(' ', $actions));

				$title = $this->CroogoHtml->link($menu->title, array(
					'controller' => 'Links',
					'?' => array(
						'menu_id' => $menu->id
					)
				));
				if ($menu->status === CroogoStatus::PREVIEW) {
					$title .= ' ' . $this->CroogoHtml->tag('span', __d('croogo', 'preview'),
						array('class' => 'label label-warning')
					);
				}

				$status = $this->element('Croogo/Croogo.admin/toggle', array(
					'id' => $menu->id,
					'status' => $menu->status,
				));

				$rows[] = array(
					$menu->id,
					$title,
					$menu->alias,
					$menu->link_account,
					$status,
					$this->CroogoHtml->div('item-actions', $actions),
				);
			endforeach;

			echo $this->CroogoHtml->tableCells($rows);
			?>
		</table>
	</div>
</div>
