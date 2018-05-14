<?php

$this->assign('title', __d('croogo', 'Search Results: %s', h($q)));

?>
<div class="nodes search">
    <h2><?= $this->fetch('title') ?></h2>

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
            echo $this->Text->highlight(
                $this->Nodes->excerpt(['body' => true]),
                $q,
                [
                    'format' => '<span class="text-info">\1</span>',
                ]);
            echo $this->Nodes->moreInfo();
        ?>
    </div>
    <?php
        endforeach;
    ?>

    <?= $this->element('pagination', compact('nodes', 'type')) ?>
</div>
