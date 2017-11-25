<?php
$this->assign('title', __d('croogo', 'Step 2: Build database'));
?>

<div class="install">
    <h2><?= $this->fetch('title') ?></h2>

    <p>
        <?php
        echo __d('croogo', 'Create tables and load initial data');
        ?>
    </p>
</div>
<div class="form-actions">
    <?php
    echo $this->Html->link(__d('croogo', 'Build database'), [
        'plugin' => 'Croogo/Install',
        'controller' => 'install',
        'action' => 'data',
        '?' => ['run' => 1],
    ], [
        'tooltip' => [
            'data-title' => __d('croogo', 'Click here to build your database'),
            'data-placement' => 'left',
        ],
        'button' => 'success',
        'icon' => 'none',
    ]);
    ?>
</div>
