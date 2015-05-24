<?php

namespace Croogo\Nodes\Config\Migration;
class NodesTrackableFields extends CakeMigration {

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
				'nodes' => array(
					'created_by' => array(
						'type' => 'integer',
						'after' => 'created',
					),
					'updated_by' => array(
						'type' => 'integer',
						'after' => 'updated',
					),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'nodes' => array(
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
