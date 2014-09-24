<?php
class FirstMigrationDashboard extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 * @access public
 */
	public $description = '';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'dashboards' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'key' => 'primary'),
					'alias' => array('type' => 'string', 'null' => false, 'default' => '', 'length' => 50),
					'user_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20),
					'column' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20),
					'weight' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 20),
					'collapsed' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'status' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
					'updated' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1)
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'dashboards'
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function before($direction) {
		if ($direction === 'down') {
			return false;
		}
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function after($direction) {
		return true;
	}
}
