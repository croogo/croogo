<?php $layout->setNode($node); ?>
<div id="node-<?php echo $layout->node('id'); ?>" class="node node-type-<?php echo $layout->node('type'); ?>">
    <h2><?php echo $layout->node('title'); ?></h2>
    <?php
        echo $layout->nodeInfo();
        echo $layout->nodeBody();
        echo $layout->nodeMoreInfo();
    ?>
</div>

<div id="comments" class="node-comments">
<?php
    $type = $types_for_layout[$layout->node('type')];

    if ($type['Type']['comment_status'] > 0 && $layout->node('comment_status') > 0) {
        echo $this->element('comments');
    }

    if ($type['Type']['comment_status'] == 2 && $layout->node('comment_status') == 2) {
        echo $this->element('comments_form');
    }
?>
</div>