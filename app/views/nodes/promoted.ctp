<div class="nodes promoted">
    <?php
        if (count($nodes) == 0) {
            __('No items found.');
        } else {
            foreach ($this->params['named'] AS $nn => $nv) {
                $paginator->options['url'][$nn] = $nv;
            }
        }
    ?>

    <?php foreach ($nodes AS $node) { ?>
    <div id="node-<?php echo $node['Node']['id']; ?>" class="node node-type-<?php echo $node['Node']['type']; ?>">
        <h2><?php echo $html->link($node['Node']['title'], $node['Node']['url']); ?></h2>
        <?php 
            echo $this->element('node_info', array('node' => $node));
            echo $this->element('node_body', array('node' => $node));
            echo $this->element('node_more_info', array('node' => $node));
        ?>
    </div>
    <?php } ?>

    <div class="paging"><?php echo $paginator->numbers(); ?></div>
</div>