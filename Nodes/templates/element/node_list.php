<?php
/**
 * @var \App\View\AppView $this
 * @var string $alias
 * @var mixed $nodesList
 * @var array $options
 */
?>
<div id="node-list-<?= $alias ?>" class="node-list">
    <ul>
    <?php
    foreach ($nodesList as $node) {
        $node->url->prefix = false;
        if ($options['link']) {
            echo '<li>';
            echo $this->Html->link($node->title, $node->url->getUrl(0));
            echo '</li>';
        } else {
            echo '<li>' . $node->title . '</li>';
        }
    }
    ?>
    </ul>
</div>
