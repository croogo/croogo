<?php
$this->assign('title', __d('croogo', 'Step 2: Build database'));
?>

<p>
    <?php
    echo __d('croogo', 'Create tables and load initial data');
    ?>
</p>

<?php $this->start('buttons');

    echo $this->Html->link(__d('croogo', 'Build database'), [
        'plugin' => 'Croogo/Install',
        'controller' => 'Install',
        'action' => 'data',
        '?' => ['run' => 1],
    ], [
        'tooltip' => [
            'data-title' => __d('croogo', 'Click here to build your database'),
            'data-placement' => 'left',
        ],
        'button' => 'success',
    ]);

    $this->end();
