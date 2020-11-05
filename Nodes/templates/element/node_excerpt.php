<?php
/**
 * @var \App\View\AppView $this
 * @var string $excerpt
 * @var \Croogo\Nodes\Model\Entity\Node $node
 */
?>
<?php if (isset($excerpt)) : ?>
<div class="node-excerpt my-2">
    <?= $excerpt ?>
    <div class="text-right">
    <?=
        $this->Html->link(__d('croogo', 'Read'), [
            'action' => 'view',
            'type' => $node->type,
            'slug' => $node->slug,
        ], [
            'class' => 'btn btn-sm btn-outline-info',
        ])
    ?>
    </div>
</div>
<?php endif ?>
