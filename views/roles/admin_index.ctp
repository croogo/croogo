<div class="roles index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $html->link(__('New Role', true), array('action'=>'add')); ?></li>
        </ul>
    </div>

    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $html->tableHeaders(array(
            $paginator->sort('id'),
            $paginator->sort('title'),
            $paginator->sort('alias'),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($roles AS $role) {
            $actions  = $html->link(__('Edit', true), array('controller' => 'roles', 'action' => 'edit', $role['Role']['id']));
            $actions .= ' ' . $layout->adminRowActions($role['Role']['id']);
            $actions .= ' ' . $html->link(__('Delete', true), array(
                'controller' => 'roles',
                'action' => 'delete',
                $role['Role']['id'],
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?', true));

            $rows[] = array(
                $role['Role']['id'],
                $role['Role']['title'],
                $role['Role']['alias'],
                $actions,
            );
        }

        echo $html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
</div>

<div class="paging"><?php echo $paginator->numbers(); ?></div>
<div class="counter"><?php echo $paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true))); ?></div>
