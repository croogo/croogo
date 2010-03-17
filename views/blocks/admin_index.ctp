<div class="blocks index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $html->link(__('New Block', true), array('action'=>'add')); ?></li>
        </ul>
    </div>

    <?php echo $form->create('Block', array('url' => array('controller' => 'blocks', 'action' => 'process'))); ?>
    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders = $html->tableHeaders(array(
            '',
            $paginator->sort('id'),
            $paginator->sort('title'),
            $paginator->sort('alias'),
            $paginator->sort('region_id'),
            $paginator->sort('status'),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($blocks AS $block) {
            $actions  = $html->link(__('Move up', true), array('controller' => 'blocks', 'action' => 'moveup', $block['Block']['id']));
            $actions .= ' ' . $html->link(__('Move down', true), array('controller' => 'blocks', 'action' => 'movedown', $block['Block']['id']));
            $actions .= ' ' . $html->link(__('Edit', true), array('controller' => 'blocks', 'action' => 'edit', $block['Block']['id']));
            $actions .= ' ' . $layout->adminRowActions($block['Block']['id']);
            $actions .= ' ' . $html->link(__('Delete', true), array(
                'controller' => 'blocks',
                'action' => 'delete',
                $block['Block']['id'],
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?', true));

            $rows[] = array(
                $form->checkbox('Block.'.$block['Block']['id'].'.id'),
                $block['Block']['id'],
                $html->link($block['Block']['title'], array('controller' => 'blocks', 'action' => 'edit', $block['Block']['id'])),
                $block['Block']['alias'],
                $block['Region']['title'],
                $layout->status($block['Block']['status']),
                $actions,
            );
        }

        echo $html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
    <div class="bulk-actions">
    <?php
        echo $form->input('Block.action', array(
            'label' => false,
            'options' => array(
                'publish' => __('Publish', true),
                'unpublish' => __('Unpublish', true),
                'delete' => __('Delete', true),
            ),
            'empty' => true,
        ));
        echo $form->end(__('Submit', true));
    ?>
    </div>
</div>

<div class="paging"><?php echo $paginator->numbers(); ?></div>
<div class="counter"><?php echo $paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true))); ?></div>
