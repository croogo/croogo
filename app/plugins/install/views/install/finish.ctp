<div class="install">
    <h2><?php echo $this->pageTitle; ?></h2>

    <p>
        Admin panel: <?php echo $html->link(Router::url('/admin', true), Router::url('/admin', true)); ?><br />
        Username: admin<br />
        Password: password
    </p>

    <br />
    <br />

    <p>
        Delete the installation directory <strong>/app/plugins/install</strong>.
    </p>

    <br />
    <br />

    <?php
        echo $html->link(__('Click here to delete installation files', true), array(
            'plugin' => 'install',
            'controller' => 'install',
            'action' => 'finish',
            'delete' => 1,
        ));
    ?>
</div>