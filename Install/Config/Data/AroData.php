<?php
class AroData {

	public $table = 'aros';

	public $records = array(
		array(
			'id' => '1',
			'parent_id' => '2',
			'model' => 'Role',
			'foreign_key' => '1',
			'alias' => 'Role-admin',
			'lft' => '3',
			'rght' => '4'
		),
		array(
			'id' => '2',
			'parent_id' => '3',
			'model' => 'Role',
			'foreign_key' => '2',
			'alias' => 'Role-registered',
			'lft' => '2',
			'rght' => '5'
		),
		array(
			'id' => '3',
			'parent_id' => '',
			'model' => 'Role',
			'foreign_key' => '3',
			'alias' => 'Role-public',
			'lft' => '1',
			'rght' => '6'
		),
	);

}
