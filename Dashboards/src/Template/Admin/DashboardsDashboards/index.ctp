<?php
$this->assign('title', __d('croogo', 'Dashboards'));

$this->extend('Croogo/Core./Common/admin_index');

$this->CroogoHtml
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Dashboards'), array('action' => 'index'));

$this->set('showActions', false);

$this->append('table-heading');
	$tableHeaders = $this->CroogoHtml->tableHeaders(array(
		$this->Paginator->sort('id'),
		$this->Paginator->sort('alias'),
		$this->Paginator->sort('column'),
		$this->Paginator->sort('collapsed'),
		$this->Paginator->sort('status'),
		$this->Paginator->sort('updated'),
		$this->Paginator->sort('created'),
		__d('croogo', 'Actions'),
	));
	echo $this->CroogoHtml->tag('thead', $tableHeaders);
$this->end();

$this->append('table-body');
foreach ($dashboards as $dashboard):
?>
	<tr>
		<td><?php echo h($dashboard->id); ?>&nbsp;</td>
		<td><?php echo h($dashboard->alias); ?>&nbsp;</td>
		<td><?php echo $this->Dashboards->columnName($dashboard->column); ?>&nbsp;</td>
		<td>
			<?php
			if ($dashboard->collapsed):
				echo $this->Layout->status($dashboard->collapsed);
			endif;
			?>&nbsp;
		</td>
		<td>
			<?php
				echo $this->element('Croogo/Core.admin/toggle', array(
					'id' => $dashboard->id,
					'status' => (int)$dashboard->status,
				));
			?>
		</td>
		<td><?php echo h($dashboard['DashboardsDashboard']['updated']); ?>&nbsp;</td>
		<td><?php echo h($dashboard['DashboardsDashboard']['created']); ?>&nbsp;</td>
		<td class="item-actions">
		<?php
			$actions = array();
			$actions[] = $this->Croogo->adminRowAction('',
				array('controller' => 'dashboards_dashboards', 'action' => 'moveup', $dashboard->id),
				array(
					'icon' => $this->Theme->getIcon('move-up'),
					'tooltip' => __d('croogo', 'Move up'),
				)
			);
			$actions[] = $this->Croogo->adminRowAction('',
				array('controller' => 'dashboards_dashboards', 'action' => 'movedown', $dashboard->id),
				array(
					'icon' => $this->Theme->getIcon('move-down'),
					'tooltip' => __d('croogo', 'Move down'),
				)
			);
			$actions[] = $this->Croogo->adminRowAction('',
				array('action' => 'delete', $dashboard->id),
				array('icon' => $this->Theme->getIcon('delete'), 'escape' => true),
				__d('croogo', 'Are you sure you want to delete # %s?', $dashboard->id)
			);
			echo implode(' ', $actions);
		?>
		</td>
	</tr>
<?php
endforeach;
$this->end();
