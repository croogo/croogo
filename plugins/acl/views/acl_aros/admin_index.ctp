<div class="acl_aros index">
    <h2><?php __('Aros');?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link(__('New Aro', true), array('action'=>'add')); ?></li>
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
        foreach ($aros AS $aro) {
            $actions  = $this->Html->link(__('Edit', true), array('action' => 'edit', $aro['AclAro']['id']));
            $actions .= ' ' . $this->Html->link(__('Delete', true), array(
                'action' => 'delete',
                $aro['AclAro']['id'],
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?', true));

            $rows[] = array(
                $aro['AclAro']['id'],
                $aro['AclAro']['parent_id'],
                $aro['AclAro']['model'],
                $this->Html->link($aro['AclAro']['foreign_key'], array('plugin' => 0, 'controller' => Inflector::pluralize(strtolower($aro['AclAro']['model'])), 'action' => 'edit', $aro['AclAro']['foreign_key'])),
                $aro['AclAro']['alias'],
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