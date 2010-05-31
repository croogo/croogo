<?php
    $html->script(array('nodes'), false);
?>
<div class="nodes index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $html->link(__('Create content', true), array('action'=>'create')); ?></li>
            <li><?php echo $html->link(__('Filter', true), '#', array('class' => 'filter')); ?></li>
        </ul>
    </div>

    <?php
    	if (isset($this->params['named'])) {
            foreach ($this->params['named'] AS $nn => $nv) {
                $paginator->options['url'][] = $nn . ':' . $nv;
            }
        }

        echo $this->element('admin/nodes_filter');
    ?>

    <?php echo $form->create('Node', array('url' => array('controller' => 'nodes', 'action' => 'process'))); ?>
    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $html->tableHeaders(array(
            '',
            $paginator->sort('id'),
            $paginator->sort('title'),
            $paginator->sort('type'),
            $paginator->sort('user_id'),
            $paginator->sort('status'),
            $paginator->sort('promote'),
            //$paginator->sort('created'),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($nodes AS $node) {
            $actions  = $html->link(__('Edit', true), array('action' => 'edit', $node['Node']['id']));
            $actions .= ' ' . $layout->adminRowActions($node['Node']['id']);
            $actions .= ' ' . $html->link(__('Delete', true), array(
                'action' => 'delete',
                $node['Node']['id'],
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?', true));

            $rows[] = array(
                $form->checkbox('Node.'.$node['Node']['id'].'.id'),
                $node['Node']['id'],
                $html->link($node['Node']['title'], array(
                    'admin' => false,
                    'controller' => 'nodes',
                    'action' => 'view',
                    'type' => $node['Node']['type'],
                    'slug' => $node['Node']['slug'],
                )),
                $node['Node']['type'],
                $node['User']['username'],
                $layout->status($node['Node']['status']),
                $layout->status($node['Node']['promote']),
                //$node['Node']['created'],
                $actions,
            );
        }

        echo $html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>

    <div class="bulk-actions">
    <?php
        echo $form->input('Node.action', array(
            'label' => false,
            'options' => array(
                'publish' => __('Publish', true),
                'unpublish' => __('Unpublish', true),
                'promote' => __('Promote', true),
                'unpromote' => __('Unpromote', true),
                //'delete' => __('Delete', true),
            ),
            'empty' => true,
        ));
        echo $form->end(__('Submit', true));
    ?>
    </div>
</div>

<div class="paging"><?php echo $paginator->numbers(); ?></div>
<div class="counter"><?php echo $paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true))); ?></div>
