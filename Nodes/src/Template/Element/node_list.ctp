<div id="node-list-<?= $alias ?>" class="node-list">
    <ul>
    <?php
        foreach ($nodesList as $node) {
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
