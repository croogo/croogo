<?php

namespace Croogo\Settings\Config\Migration;
class SettingsTrackableFields extends CakeMigration {

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
				'settings' => array(
					'created' => array(
						'type' => 'datetime',
						'after' => 'params',
						'null' => true,
					),
					'created_by' => array(
						'type' => 'integer',
						'length' => 20,
						'after' => 'created',
					),
					'updated' => array(
						'type' => 'datetime',
						'after' => 'created_by',
						'null' => true,
					),
					'updated_by' => array(
						'type' => 'integer',
						'length' => 20,
						'after' => 'updated',
					),
				),
				'languages' => array(
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
				'settings' => array(
					'created',
					'created_by',
					'updated',
					'updated_by',
				),
				'languages' => array(
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
