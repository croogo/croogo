<?php
$this->viewVars['title_for_layout'] = __d('croogo', 'Dashboards');
$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Dashboards'), array('action' => 'index'));

$this->set('showActions', false);

$this->append('table-heading');
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Paginator->sort('id'),
		$this->Paginator->sort('alias'),
		$this->Paginator->sort('column'),
		$this->Paginator->sort('collapsed'),
		$this->Paginator->sort('status'),
		$this->Paginator->sort('updated'),
		$this->Paginator->sort('created'),
		__d('croogo', 'Actions'),
	));
	echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->append('table-body');
foreach ($dashboards as $dashboard):
?>
	<tr>
		<td><?php echo h($dashboard['DashboardsDashboard']['id']); ?>&nbsp;</td>
		<td><?php echo h($dashboard['DashboardsDashboard']['alias']); ?>&nbsp;</td>
		<td><?php echo $this->Dashboards->columnName($dashboard['DashboardsDashboard']['column']); ?>&nbsp;</td>
		<td>
			<?php
			if ($dashboard['DashboardsDashboard']['collapsed']):
				echo $this->Layout->status($dashboard['DashboardsDashboard']['collapsed']);
			endif;
			?>&nbsp;
		</td>
		<td>
			<?php
				echo $this->element('admin/toggle', array(
					'id' => $dashboard['DashboardsDashboard']['id'],
					'status' => (int)$dashboard['DashboardsDashboard']['status'],
				));
			?>
		</td>
		<td><?php echo h($dashboard['DashboardsDashboard']['updated']); ?>&nbsp;</td>
		<td><?php echo h($dashboard['DashboardsDashboard']['created']); ?>&nbsp;</td>
		<td class="item-actions">
		<?php
			$actions = array();
			$actions[] = $this->Croogo->adminRowAction('',
				array('controller' => 'dashboards_dashboards', 'action' => 'moveup', $dashboard['DashboardsDashboard']['id']),
				array(
					'icon' => $this->Theme->getIcon('move-up'),
					'tooltip' => __d('croogo', 'Move up'),
				)
			);
			$actions[] = $this->Croogo->adminRowAction('',
				array('controller' => 'dashboards_dashboards', 'action' => 'movedown', $dashboard['DashboardsDashboard']['id']),
				array(
					'icon' => $this->Theme->getIcon('move-down'),
					'tooltip' => __d('croogo', 'Move down'),
				)
			);
			$actions[] = $this->Croogo->adminRowAction('',
				array('action' => 'delete', $dashboard['DashboardsDashboard']['id']),
				array('icon' => $this->Theme->getIcon('delete'), 'escape' => true),
				__d('croogo', 'Are you sure you want to delete # %s?', $dashboard['DashboardsDashboard']['id'])
			);
			echo implode(' ', $actions);
		?>
		</td>
	</tr>
<?php
endforeach;
$this->end();
