<?php
class NodeData {

	public $table = 'nodes';

	public $records = array(
		array(
			'id' => '1',
			'parent_id' => '',
			'user_id' => '1',
			'title' => 'Hello World',
			'slug' => 'hello-world',
			'body' => '<p>Welcome to Croogo. This is your first post. You can edit or delete it from the admin panel.</p>',
			'excerpt' => '',
			'status' => '1',
			'mime_type' => '',
			'comment_status' => '2',
			'comment_count' => '1',
			'promote' => '1',
			'path' => '/blog/hello-world',
			'terms' => '{"1":"uncategorized"}',
			'sticky' => '0',
			'lft' => '1',
			'rght' => '2',
			'visibility_roles' => '',
			'type' => 'blog',
			'updated' => '2009-12-25 11:00:00',
			'created' => '2009-12-25 11:00:00'
		),
		array(
			'id' => '2',
			'parent_id' => '',
			'user_id' => '1',
			'title' => 'About',
			'slug' => 'about',
			'body' => '<p>This is an example of a Croogo page, you could edit this to put information about yourself or your site.</p>',
			'excerpt' => '',
			'status' => '1',
			'mime_type' => '',
			'comment_status' => '0',
			'comment_count' => '0',
			'promote' => '0',
			'path' => '/about',
			'terms' => '',
			'sticky' => '0',
			'lft' => '1',
			'rght' => '2',
			'visibility_roles' => '',
			'type' => 'page',
			'updated' => '2009-12-25 22:00:00',
			'created' => '2009-12-25 22:00:00'
		),
	);

}
