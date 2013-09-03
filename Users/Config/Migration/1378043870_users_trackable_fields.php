<?php

class UsersTrackableFields extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 * @access public
 */
	public $description = 'Adding Trackable Fields';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(

				'users' => array(
					'created_by' => array(
						'type' => 'integer',
						'length' => 20,
						'after' => 'created',
					),
					'updated_by' => array(
						'type' => 'integer',
						'length' => 20,
						'after' => 'updated',
					),
				),

				'roles' => array(
					'created_by' => array(
						'type' => 'integer',
						'length' => 20,
						'after' => 'created',
					),
					'updated_by' => array(
						'type' => 'integer',
						'length' => 20,
						'after' => 'updated',
					),
				),

			),
		),
		'down' => array(
			'drop_field' => array(
				'users' => array(
					'created_by',
					'updated_by',
				),
				'roles' => array(
					'created_by',
					'updated_by',
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
