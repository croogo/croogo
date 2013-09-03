<?php

class UsersEnlargeTimezone extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 * @access public
 */
	public $description = 'Updating User timezone';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'users' => array(
					'timezone' => array(
						'type' => 'string',
						'length' => 40,
						'after' => 'created',
					),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'users' => array(
					'timezone' => array(
						'type' => 'string',
						'length' => 10,
					),
				),
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
