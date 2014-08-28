<?php
class AddWysisygEnableField extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = '';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'types' => array(
					'format_use_wysiwyg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'after' => 'format_show_date'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'types' => array('format_use_wysiwyg',),
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 */
	public function after($direction) {
		return true;
	}
}
