<div id="node-<?php echo $node['Node']['id']; ?>" class="node node-type-<?php echo $node['Node']['type']; ?>">
    <h2><?php echo $node['Node']['title']; ?></h2>
    <?php
        echo $this->element('node_info');
        echo $this->element('node_body');
        echo $this->element('node_more_info');
    ?>
</div>

<div id="comments" class="node-comments">
<?php
    $type = $types_for_layout[$node['Node']['type']];

    if ($type['Type']['comment_status'] > 0 && $node['Node']['comment_status'] > 0) {
        echo $this->element('comments');
    }

    if ($type['Type']['comment_status'] == 2 && $node['Node']['comment_status'] == 2) {
        echo $this->element('comments_form');
    }
?>
</div>