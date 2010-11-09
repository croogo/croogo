<div class="acl_acos index">
    <h2><?php __('Acos');?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link(__('New Aco', true), array('action'=>'add')); ?></li>
        </ul>
    </div>

    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders = $this->Html->tableHeaders(array(
            $paginator->sort('id'),
            $paginator->sort('parent_id'),
            $paginator->sort('model'),
            $paginator->sort('foreign_key'),
            $paginator->sort('alias'),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($acos AS $aco) {
            $actions  = $this->Html->link(__('Edit', true), array('action' => 'edit', $aco['AclAco']['id']));
            $actions .= ' ' . $this->Html->link(__('Delete', true), array(
                'action' => 'delete',
                $aco['AclAco']['id'],
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?', true));

            $rows[] = array(
                $aco['AclAco']['id'],
                $aco['AclAco']['parent_id'],
                $aco['AclAco']['model'],
                $aco['AclAco']['foreign_key'],
                $aco['AclAco']['alias'],
                $actions,
            );
        }

        echo $this->Html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
</div>

<div class="paging"><?php echo $paginator->numbers(); ?></div>
<div class="counter"><?php echo $paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true))); ?></div>