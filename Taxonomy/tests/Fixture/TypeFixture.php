<?php

namespace Croogo\Taxonomy\Test\Fixture;
class TypeFixture extends CroogoTestFixture {

	public $name = 'Type';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
		'title' => ['type' => 'string', 'null' => false, 'default' => null],
		'alias' => ['type' => 'string', 'null' => false, 'default' => null],
		'description' => ['type' => 'text', 'null' => false, 'default' => null],
		'format_show_author' => ['type' => 'boolean', 'null' => false, 'default' => '1'],
		'format_show_date' => ['type' => 'boolean', 'null' => false, 'default' => '1'],
		'comment_status' => ['type' => 'integer', 'null' => false, 'default' => '1', 'length' => 1],
		'comment_approve' => ['type' => 'boolean', 'null' => false, 'default' => '1'],
		'comment_spam_protection' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'comment_captcha' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'params' => ['type' => 'text', 'null' => true, 'default' => null],
		'plugin' => ['type' => 'string', 'null' => true, 'default' => null],
		'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'PRIMARY' => ['type' => 'unique', 'columns' => 'id'], 'type_alias' => ['type' => 'unique', 'columns' => 'alias']],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
	);

	public $records = array(
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
