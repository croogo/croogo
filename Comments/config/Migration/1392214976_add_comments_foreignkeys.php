<?php

namespace Croogo\Comments\Config\Migration;
class AddCommentsForeignKeys extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 * @access public
 */
	public $description = 'Add comments foreign keys';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'comments' => array(
					'model' => array(
						'type' => 'string',
						'length' => 50,
						'after' => 'parent_id',
						'null' => false,
						'default' => 'Node',
					),
					'indexes' => array(
						'comments_fk' => array(
							'column' => array('model', 'foreign_key'),
						),
					),
				),
			),
			'rename_field' => array(
				'comments' => array(
					'node_id' => 'foreign_key',
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'comments' => array(
					'model',
					'indexes' => array('comments_fk'),
				),
			),
			'rename_field' => array(
				'comments' => array(
					'foreign_key' => 'node_id',
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
