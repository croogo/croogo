<?php
echo $this->Form->create(false, array(
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
		echo $this->Form->input('datasource', array(
			'placeholder' => __d('croogo', 'Database'),
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
		echo $this->Form->input('host', array(
			'placeholder' => __d('croogo', 'Host'),
			'default' => 'localhost',
			'tooltip' => __d('croogo', 'Database hostname or IP Address'),
			'before' => '<span class="add-on"><i class="icon-home"></i></span>',
			'div' => 'input input-prepend',
		));
		echo $this->Form->input('login', array(
			'placeholder' => __d('croogo', 'Login'),
			'default' => 'root',
			'tooltip' => __d('croogo', 'Database login/username'),
			'before' => '<span class="add-on"><i class="icon-user"></i></span>',
			'div' => 'input input-prepend',
		));
		echo $this->Form->input('password', array(
			'placeholder' => __d('croogo', 'Password'),
			'tooltip' => __d('croogo', 'Database password'),
			'before' => '<span class="add-on"><i class="icon-key"></i></span>',
			'div' => 'input input-prepend',
		));
		echo $this->Form->input('database', array(
			'placeholder' => __d('croogo', 'Name'),
			'default' => 'croogo',
			'tooltip' => __d('croogo', 'Database name'),
			'before' => '<span class="add-on"><i class="icon-briefcase"></i></span>',
			'div' => 'input input-prepend',
		));
		echo $this->Form->input('prefix', array(
			'placeholder' => __d('croogo', 'Prefix'),
			'tooltip' => __d('croogo', 'Table prefix (leave blank if unknown)'),
			'before' => '<span class="add-on"><i class="icon-minus"></i></span>',
			'div' => 'input input-prepend',
		));
		echo $this->Form->input('port', array(
			'placeholder' => __d('croogo', 'Port'),
			'tooltip' => __d('croogo', 'Database port (leave blank if unknown)'),
			'before' => '<span class="add-on"><i class="icon-asterisk"></i></span>',
			'div' => 'input input-prepend',
		));
	?>
</div>
<div class="form-actions">
	<?php echo $this->Form->submit('Submit', array('button' => 'success', 'div' => 'input submit')); ?>
</div>
<?php echo $this->Form->end(); ?>