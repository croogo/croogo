<?php

if (isset($dashboard['id'])):
    $dataId = 'data-id="' . h($dashboard['id']) . '"';
else:
    $dataId = null;
endif;

?>
<div class="card card-<?php echo $alias; ?> dashboard-card" id="<?php echo $alias ?>" <?= $dataId ?>>
    <div class="card-header">
        <i class="fa fa-list move-handle"></i>
        <?php echo $dashboard['title'] ?>
        <a class="toggle-icon float-right" data-toggle="collapse" data-target="#<?php echo $alias ?>-content">
            <?php echo($dashboard['collapsed'] ? '<i class="fa fa-plus"></i>' : '<i class="fa fa-minus"></i>') ?>
        </a>
    </div>
    <div class="card-body <?= $dashboard['collapsed'] ? 'collapse' : 'collapse show' ?>" id="<?php echo $alias ?>-content">
        <?php $cell = $this->cell($dashboard['cell'], $dashboard['arguments'],
            ['cache' => $dashboard['cache'], 'alias' => $alias, 'dashboard' => $dashboard]); ?>
        <?= $cell; ?>
    </div>
</div>
