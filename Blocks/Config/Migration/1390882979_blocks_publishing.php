<?php

class BlocksPublishingFields extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 * @access public
 */
	public $description = 'Adding/modifying publishing related fields';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'blocks' => array(
					'status' => array(
						'type' => 'integer',
						'length' => 1,
					),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'blocks' => array(
					'status' => array(
						'type' => 'boolean',
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
		if ($direction == 'down') {
			return Configure::read('debug') > 0;
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
