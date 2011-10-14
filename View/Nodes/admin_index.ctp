<?php
    $this->Html->script(array('nodes'), false);
?>
<div class="nodes index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link(__('Create content'), array('action'=>'create')); ?></li>
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

    <?php echo $this->Form->create('Node', array('url' => array('controller' => 'nodes', 'action' => 'process'))); ?>
    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $this->Html->tableHeaders(array(
            '',
            $paginator->sort('id'),
            $paginator->sort('title'),
            $paginator->sort('type'),
            $paginator->sort('user_id'),
            $paginator->sort('status'),
            $paginator->sort('promote'),
            //$paginator->sort('created'),
            __('Actions'),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($nodes AS $node) {
            $actions  = $this->Html->link(__('Edit'), array('action' => 'edit', $node['Node']['id']));
            $actions .= ' ' . $this->Layout->adminRowActions($node['Node']['id']);
            $actions .= ' ' . $this->Html->link(__('Delete'), array(
                'action' => 'delete',
                $node['Node']['id'],
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?'));

            $rows[] = array(
                $this->Form->checkbox('Node.'.$node['Node']['id'].'.id'),
                $node['Node']['id'],
                $this->Html->link($node['Node']['title'], array(
                    'admin' => false,
                    'controller' => 'nodes',
                    'action' => 'view',
                    'type' => $node['Node']['type'],
                    'slug' => $node['Node']['slug'],
                )),
                $node['Node']['type'],
                $node['User']['username'],
                $this->Layout->status($node['Node']['status']),
                $this->Layout->status($node['Node']['promote']),
                //$node['Node']['created'],
                $actions,
            );
        }

        echo $this->Html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>

    <div class="bulk-actions">
    <?php
        echo $this->Form->input('Node.action', array(
            'label' => false,
            'options' => array(
                'publish' => __('Publish'),
                'unpublish' => __('Unpublish'),
                'promote' => __('Promote'),
                'unpromote' => __('Unpromote'),
                //'delete' => __('Delete'),
            ),
            'empty' => true,
        ));
        echo $this->Form->end(__('Submit'));
    ?>
    </div>
</div>

<div class="paging"><?php echo $paginator->numbers(); ?></div>
<div class="counter"><?php echo $paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'))); ?></div>
