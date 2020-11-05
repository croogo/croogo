<?php
/**
 * @var \App\View\AppView $this
 * @var object $term
 * @var object $type
 * @var object $vocabulary
 * @var \Croogo\Nodes\Model\Entity\Node[]|\Cake\Collection\CollectionInterface $nodes
 */

$titles = [];
if (isset($vocabulary)) :
    $titles[] = $vocabulary->title;
endif;
if (isset($term)) :
    $titles[] = $term->title;
endif;
if (isset($type)) :
    $titles[] = $type->title;
endif;
$this->assign('title', implode(' | ', $titles));

?>
<div class="nodes">

    <?php
    if (count($nodes) == 0) {
        echo __d('croogo', 'No items found.');
    }
    ?>

    <?php
    foreach ($nodes as $node) :
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
