<div class="install">
    <h2><?php echo $title_for_layout; ?></h2>

    <p>
        Admin panel: <?php echo $this->Html->link(Router::url('/admin', true), Router::url('/admin', true)); ?><br />
        Username: admin<br />
        Password: password
    </p>

    <br />
    <br />

    <p>
        Delete the installation directory <strong>/app/Plugins/Install</strong>.
    </p>

    <br />
    <br />

    <?php
        echo $this->Html->link(__('Click here to delete installation files'), array(
            'plugin' => 'install',
            'controller' => 'install',
            'action' => 'finish',
            'delete' => 1,
        ));
    ?>
</div>