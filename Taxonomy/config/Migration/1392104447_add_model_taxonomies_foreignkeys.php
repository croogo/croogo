<?php

namespace Croogo\Taxonomy\Config\Migration;
class AddModelTaxonomyForeignKey extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 * @access public
 */
	public $description = 'Add model_taxonomies foreign keys';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'model_taxonomies' => array(
					'model' => array(
						'type' => 'string',
						'length' => 50,
						'after' => 'id',
						'null' => false,
						'default' => 'Node',
					),
				),
			),
			'rename_field' => array(
				'model_taxonomies' => array(
					'node_id' => 'foreign_key',
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'model_taxonomies' => array(
					'model',
				),
			),
			'rename_field' => array(
				'model_taxonomies' => array(
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
		$this->generateModel('Type', 'types')->updateAll(
			array('plugin' => null),
			array('plugin' => '')
		);
		$this->generateModel('Vocabulary', 'vocabularies')->updateAll(
			array('plugin' => null),
			array('plugin' => '')
		);
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
