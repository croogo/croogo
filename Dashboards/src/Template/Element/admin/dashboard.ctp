<?php

if (isset($dashboard['id'])) :
    $dataId = 'data-id="' . h($dashboard['id']) . '"';
else :
    $dataId = null;
endif;

?>
<div class="card card-<?= $alias ?> dashboard-card" id="<?= $alias ?>" <?= $dataId ?>>
    <div class="card-header">
        <?= $this->Html->icon('list', ['class' => 'move-handle']) ?>
        <?= $dashboard['title'] ?>
        <a class="toggle-icon float-right" data-toggle="collapse" data-target="#<?= $alias ?>-content">
            <?=($dashboard['collapsed'] ? $this->Html->icon('plus') : $this->Html->icon('minus')) ?>
        </a>
    </div>
    <div class="card-body <?= $dashboard['collapsed'] ? 'collapse' : 'collapse show' ?>" id="<?= $alias ?>-content">
        <?php $cell = $this->cell(
            $dashboard['cell'],
            $dashboard['arguments'],
            ['cache' => $dashboard['cache'], 'alias' => $alias, 'dashboard' => $dashboard]
        ) ?>
        <?= $cell ?>
    </div>
</div>
