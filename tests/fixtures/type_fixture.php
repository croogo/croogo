<?php
/* Type Fixture generated on: 2010-05-20 22:05:54 : 1274393814 */
class TypeFixture extends CakeTestFixture {
	var $name = 'Type';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'alias' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'unique'),
		'description' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'format_show_author' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'format_show_date' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'comment_status' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 1),
		'comment_approve' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'comment_spam_protection' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'comment_captcha' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'params' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'plugin' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'updated' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'alias' => array('column' => 'alias', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'title' => 'Page',
			'alias' => 'page',
			'description' => 'A page is a simple method for creating and displaying information that rarely changes, such as an \"About us\" section of a website. By default, a page entry does not allow visitor comments.',
			'format_show_author' => 0,
			'format_show_date' => 0,
			'comment_status' => 0,
			'comment_approve' => 1,
			'comment_spam_protection' => 0,
			'comment_captcha' => 0,
			'params' => '',
			'plugin' => '',
			'updated' => '2009-09-09 00:23:24',
			'created' => '2009-09-02 18:06:27'
		),
		array(
			'id' => 2,
			'title' => 'Blog',
			'alias' => 'blog',
			'description' => 'A blog entry is a single post to an online journal, or blog.',
			'format_show_author' => 1,
			'format_show_date' => 1,
			'comment_status' => 2,
			'comment_approve' => 1,
			'comment_spam_protection' => 0,
			'comment_captcha' => 0,
			'params' => '',
			'plugin' => '',
			'updated' => '2009-09-15 12:15:43',
			'created' => '2009-09-02 18:20:44'
		),
		array(
			'id' => 4,
			'title' => 'Node',
			'alias' => 'node',
			'description' => 'Default content type.',
			'format_show_author' => 1,
			'format_show_date' => 1,
			'comment_status' => 2,
			'comment_approve' => 1,
			'comment_spam_protection' => 0,
			'comment_captcha' => 0,
			'params' => '',
			'plugin' => '',
			'updated' => '2009-10-06 21:53:15',
			'created' => '2009-09-05 23:51:56'
		),
	);
}
?>