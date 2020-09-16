<?php
$this->assign('title', __d('croogo', 'Dashboards'));

$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs
        ->add(__d('croogo', 'Dashboards'), ['action' => 'index']);

$this->set('showActions', false);

$this->append('table-heading');
    $tableHeaders = $this->Html->tableHeaders([
        $this->Paginator->sort('id'),
        $this->Paginator->sort('alias'),
        $this->Paginator->sort('column'),
        $this->Paginator->sort('collapsed'),
        $this->Paginator->sort('status'),
        $this->Paginator->sort('modified'),
        $this->Paginator->sort('created'),
        __d('croogo', 'Actions'),
    ]);
    echo $tableHeaders;
    $this->end();

    $this->append('table-body');
    foreach ($dashboards as $dashboard) :
        ?>
    <tr>
        <td><?= h($dashboard->id) ?>&nbsp;</td>
        <td><?= h($dashboard->alias) ?>&nbsp;</td>
        <td><?= $this->Dashboards->columnName($dashboard->column) ?>&nbsp;</td>
        <td>
            <?php
            if ($dashboard->collapsed) :
                echo $this->Layout->status($dashboard->collapsed);
            endif;
            ?>&nbsp;
        </td>
        <td>
            <?php
                echo $this->element('Croogo/Core.admin/toggle', [
                    'id' => $dashboard->id,
                    'status' => (int)$dashboard->status,
                ]);
            ?>
        </td>
        <td><?= $this->Time->i18nFormat($dashboard->modified) ?>&nbsp;</td>
        <td><?= $this->Time->i18nFormat($dashboard->created) ?>&nbsp;</td>
        <td class="item-actions">
            <?php
            $actions = [];
            $actions[] = $this->Croogo->adminRowAction(
                '',
                ['controller' => 'Dashboards', 'action' => 'moveup', $dashboard->id],
                [
                    'icon' => $this->Theme->getIcon('move-up'),
                    'tooltip' => __d('croogo', 'Move up'),
                    'escapeTitle' => false,
                ]
            );
            $actions[] = $this->Croogo->adminRowAction(
                '',
                ['controller' => 'Dashboards', 'action' => 'movedown', $dashboard->id],
                [
                    'icon' => $this->Theme->getIcon('move-down'),
                    'tooltip' => __d('croogo', 'Move down'),
                    'escapeTitle' => false,
                ]
            );
            $actions[] = $this->Croogo->adminRowAction(
                '',
                ['action' => 'delete', $dashboard->id],
                [
                    'icon' => $this->Theme->getIcon('delete'),
                    'escape' => true,
                    'method' => 'post',
                    'escapeTitle' => false,
                ],
                __d('croogo', 'Are you sure you want to delete # %s?', $dashboard->id)
            );
            echo implode(' ', $actions);
            ?>
        </td>
    </tr>
        <?php
    endforeach;
    $this->end();
