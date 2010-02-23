<?php

class MetaFixture extends CakeTestFixture {
	var $name = 'Meta';
	var $import = 'Meta';
        var $table = 'meta';
	var $records = array(
		array(
			'id' => '23',
			'model' => 'Node',
			'foreign_key' => '20',
			'key' => 'meta_keywords',
			'value' => 'key1, key2',
			'weight' => '',
		),
	);
}

?>