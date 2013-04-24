<?php
class FirstMigrationBlocks extends CakeMigration {

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
					'blocks' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'key' => 'primary'),
					'region_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 20),
					'title' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
					'alias' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'key' => 'unique', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
					'body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
					'show_title' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
					'class' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
					'status' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'weight' => array('type' => 'integer', 'null' => true, 'default' => null),
					'element' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
					'visibility_roles' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
					'visibility_paths' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
					'visibility_php' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
					'params' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
					'updated' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'block_alias' => array('column' => 'alias', 'unique' => 1)
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
				),
				'regions' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
					'title' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
					'alias' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'key' => 'unique', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
					'block_count' => array('type' => 'integer', 'null' => false, 'default' => '0'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'region_alias' => array('column' => 'alias', 'unique' => 1)
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'blocks', 'regions'
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
