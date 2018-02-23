<?php if (isset($excerpt)): ?>
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
