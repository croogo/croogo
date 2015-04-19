<?php

namespace Croogo\Translate\Test\Fixture;
class I18nFixture extends CroogoTestFixture {

	public $name = 'I18n';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
		'locale' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 6],
		'model' => ['type' => 'string', 'null' => false, 'default' => null],
		'foreign_key' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
		'field' => ['type' => 'string', 'null' => false, 'default' => null],
		'content' => ['type' => 'text', 'null' => true, 'default' => null],
		'_indexes' => ['locale' => ['unique' => 0, 'columns' => 'locale'], 'model' => ['unique' => 0, 'columns' => 'model'], 'row_id' => ['unique' => 0, 'columns' => 'foreign_key'], 'field' => ['unique' => 0, 'columns' => 'field']],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'PRIMARY' => ['type' => 'unique', 'columns' => 'id']]
	);

	public $table = 'i18n';

	public $records = array(
	);
}
