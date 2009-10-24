<div class="install">
    <h2><?php echo $this->pageTitle; ?></h2>

    <?php
        echo $html->link(__('Click here to build your database', true), array(
            'plugin' => 'install',
            'controller' => 'install',
            'action' => 'data',
            'run' => 1,
        ));
    ?>
</div>