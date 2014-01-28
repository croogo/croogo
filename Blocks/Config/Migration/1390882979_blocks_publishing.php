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
			'create_field' => array(
				'blocks' => array(
					'publish_start' => array(
						'type' => 'datetime',
						'after' => 'params',
					),
					'publish_end' => array(
						'type' => 'datetime',
						'after' => 'publish_start',
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
			'drop_field' => array(
				'blocks' => array(
					'publish_start',
					'publish_end',
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
