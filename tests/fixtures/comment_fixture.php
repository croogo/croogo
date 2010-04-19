<?php

class CommentFixture extends CakeTestFixture {
	public $name = 'Comment';
	public $import = 'Comment';
	public $records = array(
		array(
			'id' => '13',
			'parent_id' => '',
			'node_id' => '21',
			'user_id' => '0',
			'name' => 'Mr Croogo',
			'email' => 'email@example.com',
			'website' => 'http://www.croogo.org',
			'ip' => '127.0.0.1',
			'title' => '',
			'body' => 'Hi, this is the first comment.',
			'rating' => '',
			'status' => '1',
			'notify' => '0',
			'type' => 'blog',
			'comment_type' => 'comment',
			'lft' => '1',
			'rght' => '2',
			'updated' => '2009-10-06 22:13:05',
			'created' => '2009-10-03 19:43:52',
		),
	);
}

?>