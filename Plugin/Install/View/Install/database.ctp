<div class="install form">
	<h2><?php echo $title_for_layout; ?></h2>
	<?php
		echo $this->Form->create(null, array('url' => array('plugin' => 'install', 'controller' => 'install', 'action' => 'database')));
		echo $this->Form->input('Install.datasource', array(
			'label' => 'Datasource',
			'default' => 'Database/Mysql',
			'empty' => false,
			'options' => array(
				'Database/Mysql' => 'mysql',
				'Database/Sqlite' => 'sqlite',
				'Database/Postgres' => 'postgres',
				'Database/Sqlserver' => 'mssql',
			),
		));
		echo $this->Form->input('Install.host', array('label' => 'Host', 'default' => 'localhost'));
		echo $this->Form->input('Install.login', array('label' => 'User / Login', 'default' => 'root'));
		echo $this->Form->input('Install.password', array('label' => 'Password'));
		echo $this->Form->input('Install.database', array('label' => 'Name', 'default' => 'croogo'));
		echo $this->Form->input('Install.prefix', array('label' => 'Prefix'));
		echo $this->Form->input('Install.port', array('label' => 'Port (leave blank if unknown)'));
		echo $this->Form->end('Submit');
	?>
</div>