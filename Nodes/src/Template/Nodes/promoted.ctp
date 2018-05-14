<?php

$this->assign('title', 'Home');

?>
<div class="nodes promoted">
    <?php
        if (count($nodes) == 0) {
            echo __d('croogo', 'No items found.');
        }
    ?>

    <?php
        foreach ($nodes as $node):
            $this->Nodes->set($node);
    ?>
    <div id="node-<?= $this->Nodes->field('id') ?>" class="node node-type-<?= $this->Nodes->field('type') ?>">
        <h2><?= $this->Html->link($this->Nodes->field('title'), $this->Nodes->field('url')->getUrl()) ?></h2>
        <?php
            echo $this->Nodes->info();
            echo $this->Nodes->excerpt(['body' => true]);
            echo $this->Nodes->moreInfo();
        ?>
    </div>
    <?php
        endforeach;
    ?>

    <?= $this->element('pagination', compact('nodes', 'type')) ?>
</div>
