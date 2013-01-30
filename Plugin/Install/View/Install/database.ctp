<?php
echo $this->Form->create(null, array(
	'url' => array(
		'plugin' => 'install',
		'controller' => 'install',
		'action' => 'database'
	),
), array(
	'class' => 'inline',
));
?>
<div class="install">
	<h2><?php echo $title_for_layout; ?></h2>
	<?php
		$this->Form->inputDefaults(array(
			'label' => false,
			'class' => 'span10',
		));
		echo $this->Form->input('Install.datasource', array(
			'label' => __('Database'),
			'default' => 'Database/Mysql',
			'empty' => false,
			'class' => false,
			'options' => array(
				'Database/Mysql' => 'mysql',
				'Database/Sqlite' => 'sqlite',
				'Database/Postgres' => 'postgres',
				'Database/Sqlserver' => 'mssql',
			),
		));
		echo $this->Form->input('Install.host', array(
			'placeholder' => __('Host'),
			'default' => 'localhost',
			'tooltip' => __('Database hostname or IP Address'),
			'before' => '<span class="add-on"><i class="icon-home"></i></span>',
			'div' => 'input input-prepend',
		));
		echo $this->Form->input('Install.login', array(
			'placeholder' => __('Login'),
			'default' => 'root',
			'tooltip' => __('Database login/username'),
			'before' => '<span class="add-on"><i class="icon-user"></i></span>',
			'div' => 'input input-prepend',
		));
		echo $this->Form->input('Install.password', array(
			'placeholder' => __('Password'),
			'tooltip' => __('Database password'),
			'before' => '<span class="add-on"><i class="icon-key"></i></span>',
			'div' => 'input input-prepend',
		));
		echo $this->Form->input('Install.database', array(
			'placeholder' => __('Name'),
			'default' => 'croogo',
			'tooltip' => __('Database name'),
			'before' => '<span class="add-on"><i class="icon-briefcase"></i></span>',
			'div' => 'input input-prepend',
		));
		echo $this->Form->input('Install.prefix', array(
			'placeholder' => __('Prefix'),
			'tooltip' => __('Table prefix (leave blank if unknown)'),
			'before' => '<span class="add-on"><i class="icon-minus"></i></span>',
			'div' => 'input input-prepend',
		));
		echo $this->Form->input('Install.port', array(
			'placeholder' => __('Port'),
			'tooltip' => __('Database port (leave blank if unknown)'),
			'before' => '<span class="add-on"><i class="icon-asterisk"></i></span>',
			'div' => 'input input-prepend',
		));
	?>
</div>
<div class="form-actions">
	<?php echo $this->Form->submit('Submit', array('button' => 'success', 'div' => 'input submit')); ?>
</div>
<?php echo $this->Form->end(); ?>