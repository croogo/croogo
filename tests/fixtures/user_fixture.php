<?php
/* User Fixture generated on: 2010-05-20 22:05:57 : 1274393817 */
class UserFixture extends CakeTestFixture {
	var $name = 'User';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 20, 'key' => 'primary'),
		'role_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'username' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 60),
		'password' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 50),
		'email' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'website' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'activation_key' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 60),
		'image' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'bio' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'timezone' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 10),
		'status' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'updated' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'role_id' => 1,
			'username' => 'admin',
			'password' => 'c054b152596745efa1d197b809fa7fc70ce586e5',
			'name' => 'Administrator',
			'email' => 'you@your-site.com',
			'website' => '/about',
			'activation_key' => '',
			'image' => '',
			'bio' => '',
			'timezone' => '0',
			'status' => 1,
			'updated' => '2009-10-07 22:23:27',
			'created' => '2009-04-05 00:20:34'
		),
	);
}
?>