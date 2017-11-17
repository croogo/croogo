<?php

if (isset($dashboard['id'])):
    $dataId = 'data-id="' . h($dashboard['id']) . '"';
else:
    $dataId = null;
endif;

?>
<div class="card card-<?= $alias ?> dashboard-card" id="<?= $alias ?>" <?= $dataId ?>>
    <div class="card-header">
        <i class="fa fa-list move-handle"></i>
        <?= $dashboard['title'] ?>
        <a class="toggle-icon float-right" data-toggle="collapse" data-target="#<?= $alias ?>-content">
            <?=($dashboard['collapsed'] ? '<i class="fa fa-plus"></i>' : '<i class="fa fa-minus"></i>') ?>
        </a>
    </div>
    <div class="card-body <?= $dashboard['collapsed'] ? 'collapse' : 'collapse show' ?>" id="<?= $alias ?>-content">
        <?php $cell = $this->cell($dashboard['cell'], $dashboard['arguments'],
            ['cache' => $dashboard['cache'], 'alias' => $alias, 'dashboard' => $dashboard]) ?>
        <?= $cell ?>
    </div>
</div>
