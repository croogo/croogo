<?php
$this->assign('title', __d('croogo', 'Dashboards'));

$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs
        ->add(__d('croogo', 'Dashboards'), array('action' => 'index'));

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
        <td><?= h($dashboard->id) ?>&nbsp;</td>
        <td><?= h($dashboard->alias) ?>&nbsp;</td>
        <td><?= $this->Dashboards->columnName($dashboard->column) ?>&nbsp;</td>
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
        <td><?= $this->Time->i18nFormat($dashboard->updated) ?>&nbsp;</td>
        <td><?= $this->Time->i18nFormat($dashboard->created) ?>&nbsp;</td>
        <td class="item-actions">
        <?php
            $actions = array();
            $actions[] = $this->Croogo->adminRowAction('',
                array('controller' => 'Dashboards', 'action' => 'moveup', $dashboard->id),
                array(
                    'icon' => $this->Theme->getIcon('move-up'),
                    'tooltip' => __d('croogo', 'Move up'),
                )
            );
            $actions[] = $this->Croogo->adminRowAction('',
                array('controller' => 'Dashboards', 'action' => 'movedown', $dashboard->id),
                array(
                    'icon' => $this->Theme->getIcon('move-down'),
                    'tooltip' => __d('croogo', 'Move down'),
                )
            );
            $actions[] = $this->Croogo->adminRowAction('',
                array('action' => 'delete', $dashboard->id),
                array(
                    'icon' => $this->Theme->getIcon('delete'),
                    'escape' => true,
                    'method' => 'post',
                ),
                __d('croogo', 'Are you sure you want to delete # %s?', $dashboard->id)
            );
            echo implode(' ', $actions);
        ?>
        </td>
    </tr>
<?php
endforeach;
$this->end();
