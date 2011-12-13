<div class="blocks index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link(__('New Block'), array('action'=>'add')); ?></li>
        </ul>
    </div>

    <?php echo $this->Form->create('Block', array('url' => array('controller' => 'blocks', 'action' => 'process'))); ?>
    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders = $this->Html->tableHeaders(array(
            '',
            $this->Paginator->sort('id'),
            $this->Paginator->sort('title'),
            $this->Paginator->sort('alias'),
            $this->Paginator->sort('region_id'),
            $this->Paginator->sort('status'),
            __('Actions'),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($blocks AS $block) {
            $actions  = $this->Html->link(__('Move up'), array('controller' => 'blocks', 'action' => 'moveup', $block['Block']['id']));
            $actions .= ' ' . $this->Html->link(__('Move down'), array('controller' => 'blocks', 'action' => 'movedown', $block['Block']['id']));
            $actions .= ' ' . $this->Html->link(__('Edit'), array('controller' => 'blocks', 'action' => 'edit', $block['Block']['id']));
            $actions .= ' ' . $this->Layout->adminRowActions($block['Block']['id']);
            $actions .= ' ' . $this->Html->link(__('Delete'), array(
                'controller' => 'blocks',
                'action' => 'delete',
                $block['Block']['id'],
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?'));

            $rows[] = array(
                $this->Form->checkbox('Block.'.$block['Block']['id'].'.id'),
                $block['Block']['id'],
                $this->Html->link($block['Block']['title'], array('controller' => 'blocks', 'action' => 'edit', $block['Block']['id'])),
                $block['Block']['alias'],
                $block['Region']['title'],
                $this->Layout->status($block['Block']['status']),
                $actions,
            );
        }

        echo $this->Html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
    <div class="bulk-actions">
    <?php
        echo $this->Form->input('Block.action', array(
            'label' => false,
            'options' => array(
                'publish' => __('Publish'),
                'unpublish' => __('Unpublish'),
                'delete' => __('Delete'),
            ),
            'empty' => true,
        ));
        echo $this->Form->end(__('Submit'));
    ?>
    </div>
</div>

<div class="paging"><?php echo $this->Paginator->numbers(); ?></div>
<div class="counter"><?php echo $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'))); ?></div>
