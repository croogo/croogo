<?php
class MultiUserFixture extends CakeTestFixture {

/**
 * name property
 *
 * @var string 'MultiUser'
 */
	public $name = 'MultiUser';

/**
 * fields property
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'user' => array('type' => 'string', 'null' => false),
		'email' => array('type' => 'string', 'null' => false),
		'password' => array('type' => 'string', 'null' => false),
		'token' => array('type' => 'string', 'null' => false),
		'created' => 'datetime',
		'updated' => 'datetime'
	);

/**
 * records property
 *
 * @var array
 */
	public $records = array(
		array('user' => 'mariano', 'email' => 'mariano@example.com', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99', 'token' => '12345', 'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'),
		array('user' => 'nate', 'email' => 'nate@example.com', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99', 'token' => '23456', 'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'),
		array('user' => 'larry', 'email' => 'larry@example.com', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99', 'token' => '34567', 'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'),
		array('user' => 'garrett', 'email' => 'garrett@example.com', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99', 'token' => '45678', 'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'),
		array('user' => 'chartjes', 'email' => 'chartjes@example.com', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99', 'token' => '56789', 'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'),

	);
}
