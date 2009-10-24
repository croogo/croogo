<div class="install form">
    <h2><?php echo $this->pageTitle; ?></h2>
    <?php
        echo $form->create('Install', array('url' => array('plugin' => 'install', 'controller' => 'install', 'action' => 'database')));
        echo $form->input('Install.host', array('label' => 'Host', 'value' => 'localhost'));
        echo $form->input('Install.login', array('label' => 'User / Login', 'value' => 'root'));
        echo $form->input('Install.password', array('label' => 'Password'));
        echo $form->input('Install.database', array('label' => 'Name', 'value' => 'croogo'));
        echo $form->end('Submit');
    ?>
</div>