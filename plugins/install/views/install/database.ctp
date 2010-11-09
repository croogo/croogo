<div class="install form">
    <h2><?php echo $title_for_layout; ?></h2>
    <?php
        echo $this->Form->create('Install', array('url' => array('plugin' => 'install', 'controller' => 'install', 'action' => 'database')));
        echo $this->Form->input('Install.driver', array(
            'label' => 'Driver',
            'value' => 'mysql',
            'empty' => false,
            'options' => array(
                'mysql' => 'mysql',
                'mysqli' => 'mysqli',
                'sqlite' => 'sqlite',
                'postgres' => 'postgres',
                'mssql' => 'mssql',
                'db2' => 'db2',
                'oracle' => 'oracle',
                'firebird' => 'firebird',
                'sybase' => 'sybase',
                'odbc' => 'odbc',
            ),
        ));
        //echo $this->Form->input('Install.driver', array('label' => 'Driver', 'value' => 'mysql'));
        echo $this->Form->input('Install.host', array('label' => 'Host', 'value' => 'localhost'));
        echo $this->Form->input('Install.login', array('label' => 'User / Login', 'value' => 'root'));
        echo $this->Form->input('Install.password', array('label' => 'Password'));
        echo $this->Form->input('Install.database', array('label' => 'Name', 'value' => 'croogo'));
        echo $this->Form->input('Install.port', array('label' => 'Port (leave blank if unknown)'));
        echo $this->Form->end('Submit');
    ?>
</div>