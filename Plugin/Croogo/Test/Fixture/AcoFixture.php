<?php

class AcoFixture extends CroogoTestFixture {

	public $name = 'Aco';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
		'model' => array('type' => 'string', 'null' => true),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
		'alias' => array('type' => 'string', 'null' => true),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

	public $records = array(
		array(
			'id' => 1,
			'parent_id' => null,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'controllers',
			'lft' => 1,
			'rght' => 352
		),
		array(
			'id' => 2,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'Attachments',
			'lft' => 2,
			'rght' => 13
		),
		array(
			'id' => 3,
			'parent_id' => 2,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 3,
			'rght' => 4
		),
		array(
			'id' => 4,
			'parent_id' => 2,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add',
			'lft' => 5,
			'rght' => 6
		),
		array(
			'id' => 5,
			'parent_id' => 2,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_edit',
			'lft' => 7,
			'rght' => 8
		),
		array(
			'id' => 6,
			'parent_id' => 2,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 9,
			'rght' => 10
		),
		array(
			'id' => 7,
			'parent_id' => 2,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_browse',
			'lft' => 11,
			'rght' => 12
		),
		array(
			'id' => 8,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'Blocks',
			'lft' => 14,
			'rght' => 29
		),
		array(
			'id' => 9,
			'parent_id' => 8,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 15,
			'rght' => 16
		),
		array(
			'id' => 10,
			'parent_id' => 8,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add',
			'lft' => 17,
			'rght' => 18
		),
		array(
			'id' => 11,
			'parent_id' => 8,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_edit',
			'lft' => 19,
			'rght' => 20
		),
		array(
			'id' => 12,
			'parent_id' => 8,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 21,
			'rght' => 22
		),
		array(
			'id' => 13,
			'parent_id' => 8,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_moveup',
			'lft' => 23,
			'rght' => 24
		),
		array(
			'id' => 14,
			'parent_id' => 8,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_movedown',
			'lft' => 25,
			'rght' => 26
		),
		array(
			'id' => 15,
			'parent_id' => 8,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_process',
			'lft' => 27,
			'rght' => 28
		),
		array(
			'id' => 16,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'Comments',
			'lft' => 30,
			'rght' => 45
		),
		array(
			'id' => 17,
			'parent_id' => 16,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 31,
			'rght' => 32
		),
		array(
			'id' => 18,
			'parent_id' => 16,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_edit',
			'lft' => 33,
			'rght' => 34
		),
		array(
			'id' => 19,
			'parent_id' => 16,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 35,
			'rght' => 36
		),
		array(
			'id' => 20,
			'parent_id' => 16,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_process',
			'lft' => 37,
			'rght' => 38
		),
		array(
			'id' => 21,
			'parent_id' => 16,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'index',
			'lft' => 39,
			'rght' => 40
		),
		array(
			'id' => 22,
			'parent_id' => 16,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'add',
			'lft' => 41,
			'rght' => 42
		),
		array(
			'id' => 23,
			'parent_id' => 16,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'delete',
			'lft' => 43,
			'rght' => 44
		),
		array(
			'id' => 24,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'Contacts',
			'lft' => 46,
			'rght' => 57
		),
		array(
			'id' => 25,
			'parent_id' => 24,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 47,
			'rght' => 48
		),
		array(
			'id' => 26,
			'parent_id' => 24,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add',
			'lft' => 49,
			'rght' => 50
		),
		array(
			'id' => 27,
			'parent_id' => 24,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_edit',
			'lft' => 51,
			'rght' => 52
		),
		array(
			'id' => 28,
			'parent_id' => 24,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 53,
			'rght' => 54
		),
		array(
			'id' => 29,
			'parent_id' => 24,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'view',
			'lft' => 55,
			'rght' => 56
		),
		array(
			'id' => 30,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'Filemanager',
			'lft' => 58,
			'rght' => 79
		),
		array(
			'id' => 31,
			'parent_id' => 30,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 59,
			'rght' => 60
		),
		array(
			'id' => 32,
			'parent_id' => 30,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_browse',
			'lft' => 61,
			'rght' => 62
		),
		array(
			'id' => 33,
			'parent_id' => 30,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_editfile',
			'lft' => 63,
			'rght' => 64
		),
		array(
			'id' => 34,
			'parent_id' => 30,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_upload',
			'lft' => 65,
			'rght' => 66
		),
		array(
			'id' => 35,
			'parent_id' => 30,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete_file',
			'lft' => 67,
			'rght' => 68
		),
		array(
			'id' => 36,
			'parent_id' => 30,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete_directory',
			'lft' => 69,
			'rght' => 70
		),
		array(
			'id' => 37,
			'parent_id' => 30,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_rename',
			'lft' => 71,
			'rght' => 72
		),
		array(
			'id' => 38,
			'parent_id' => 30,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_create_directory',
			'lft' => 73,
			'rght' => 74
		),
		array(
			'id' => 39,
			'parent_id' => 30,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_create_file',
			'lft' => 75,
			'rght' => 76
		),
		array(
			'id' => 40,
			'parent_id' => 30,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_chmod',
			'lft' => 77,
			'rght' => 78
		),
		array(
			'id' => 41,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'Languages',
			'lft' => 80,
			'rght' => 95
		),
		array(
			'id' => 42,
			'parent_id' => 41,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 81,
			'rght' => 82
		),
		array(
			'id' => 43,
			'parent_id' => 41,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add',
			'lft' => 83,
			'rght' => 84
		),
		array(
			'id' => 44,
			'parent_id' => 41,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_edit',
			'lft' => 85,
			'rght' => 86
		),
		array(
			'id' => 45,
			'parent_id' => 41,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 87,
			'rght' => 88
		),
		array(
			'id' => 46,
			'parent_id' => 41,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_moveup',
			'lft' => 89,
			'rght' => 90
		),
		array(
			'id' => 47,
			'parent_id' => 41,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_movedown',
			'lft' => 91,
			'rght' => 92
		),
		array(
			'id' => 48,
			'parent_id' => 41,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_select',
			'lft' => 93,
			'rght' => 94
		),
		array(
			'id' => 49,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'Links',
			'lft' => 96,
			'rght' => 111
		),
		array(
			'id' => 50,
			'parent_id' => 49,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 97,
			'rght' => 98
		),
		array(
			'id' => 51,
			'parent_id' => 49,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add',
			'lft' => 99,
			'rght' => 100
		),
		array(
			'id' => 52,
			'parent_id' => 49,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_edit',
			'lft' => 101,
			'rght' => 102
		),
		array(
			'id' => 53,
			'parent_id' => 49,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 103,
			'rght' => 104
		),
		array(
			'id' => 54,
			'parent_id' => 49,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_moveup',
			'lft' => 105,
			'rght' => 106
		),
		array(
			'id' => 55,
			'parent_id' => 49,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_movedown',
			'lft' => 107,
			'rght' => 108
		),
		array(
			'id' => 56,
			'parent_id' => 49,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_process',
			'lft' => 109,
			'rght' => 110
		),
		array(
			'id' => 57,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'Menus',
			'lft' => 112,
			'rght' => 121
		),
		array(
			'id' => 58,
			'parent_id' => 57,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 113,
			'rght' => 114
		),
		array(
			'id' => 59,
			'parent_id' => 57,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add',
			'lft' => 115,
			'rght' => 116
		),
		array(
			'id' => 60,
			'parent_id' => 57,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_edit',
			'lft' => 117,
			'rght' => 118
		),
		array(
			'id' => 61,
			'parent_id' => 57,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 119,
			'rght' => 120
		),
		array(
			'id' => 62,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'Messages',
			'lft' => 122,
			'rght' => 131
		),
		array(
			'id' => 63,
			'parent_id' => 62,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 123,
			'rght' => 124
		),
		array(
			'id' => 64,
			'parent_id' => 62,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_edit',
			'lft' => 125,
			'rght' => 126
		),
		array(
			'id' => 65,
			'parent_id' => 62,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 127,
			'rght' => 128
		),
		array(
			'id' => 66,
			'parent_id' => 62,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_process',
			'lft' => 129,
			'rght' => 130
		),
		array(
			'id' => 67,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'Nodes',
			'lft' => 132,
			'rght' => 161
		),
		array(
			'id' => 68,
			'parent_id' => 67,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 133,
			'rght' => 134
		),
		array(
			'id' => 69,
			'parent_id' => 67,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_create',
			'lft' => 135,
			'rght' => 136
		),
		array(
			'id' => 70,
			'parent_id' => 67,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add',
			'lft' => 137,
			'rght' => 138
		),
		array(
			'id' => 71,
			'parent_id' => 67,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_edit',
			'lft' => 139,
			'rght' => 140
		),
		array(
			'id' => 72,
			'parent_id' => 67,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_update_paths',
			'lft' => 141,
			'rght' => 142
		),
		array(
			'id' => 73,
			'parent_id' => 67,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 143,
			'rght' => 144
		),
		array(
			'id' => 74,
			'parent_id' => 67,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete_meta',
			'lft' => 145,
			'rght' => 146
		),
		array(
			'id' => 75,
			'parent_id' => 67,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add_meta',
			'lft' => 147,
			'rght' => 148
		),
		array(
			'id' => 76,
			'parent_id' => 67,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_process',
			'lft' => 149,
			'rght' => 150
		),
		array(
			'id' => 77,
			'parent_id' => 67,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'index',
			'lft' => 151,
			'rght' => 152
		),
		array(
			'id' => 78,
			'parent_id' => 67,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'term',
			'lft' => 153,
			'rght' => 154
		),
		array(
			'id' => 79,
			'parent_id' => 67,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'promoted',
			'lft' => 155,
			'rght' => 156
		),
		array(
			'id' => 80,
			'parent_id' => 67,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'search',
			'lft' => 157,
			'rght' => 158
		),
		array(
			'id' => 81,
			'parent_id' => 67,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'view',
			'lft' => 159,
			'rght' => 160
		),
		array(
			'id' => 82,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'Regions',
			'lft' => 162,
			'rght' => 171
		),
		array(
			'id' => 83,
			'parent_id' => 82,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 163,
			'rght' => 164
		),
		array(
			'id' => 84,
			'parent_id' => 82,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add',
			'lft' => 165,
			'rght' => 166
		),
		array(
			'id' => 85,
			'parent_id' => 82,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_edit',
			'lft' => 167,
			'rght' => 168
		),
		array(
			'id' => 86,
			'parent_id' => 82,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 169,
			'rght' => 170
		),
		array(
			'id' => 87,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'Roles',
			'lft' => 172,
			'rght' => 181
		),
		array(
			'id' => 88,
			'parent_id' => 87,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 173,
			'rght' => 174
		),
		array(
			'id' => 89,
			'parent_id' => 87,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add',
			'lft' => 175,
			'rght' => 176
		),
		array(
			'id' => 90,
			'parent_id' => 87,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_edit',
			'lft' => 177,
			'rght' => 178
		),
		array(
			'id' => 91,
			'parent_id' => 87,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 179,
			'rght' => 180
		),
		array(
			'id' => 92,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'Settings',
			'lft' => 182,
			'rght' => 201
		),
		array(
			'id' => 93,
			'parent_id' => 92,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_dashboard',
			'lft' => 183,
			'rght' => 184
		),
		array(
			'id' => 94,
			'parent_id' => 92,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 185,
			'rght' => 186
		),
		array(
			'id' => 95,
			'parent_id' => 92,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_view',
			'lft' => 187,
			'rght' => 188
		),
		array(
			'id' => 96,
			'parent_id' => 92,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add',
			'lft' => 189,
			'rght' => 190
		),
		array(
			'id' => 97,
			'parent_id' => 92,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_edit',
			'lft' => 191,
			'rght' => 192
		),
		array(
			'id' => 98,
			'parent_id' => 92,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 193,
			'rght' => 194
		),
		array(
			'id' => 99,
			'parent_id' => 92,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_prefix',
			'lft' => 195,
			'rght' => 196
		),
		array(
			'id' => 100,
			'parent_id' => 92,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_moveup',
			'lft' => 197,
			'rght' => 198
		),
		array(
			'id' => 101,
			'parent_id' => 92,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_movedown',
			'lft' => 199,
			'rght' => 200
		),
		array(
			'id' => 102,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'Terms',
			'lft' => 202,
			'rght' => 217
		),
		array(
			'id' => 103,
			'parent_id' => 102,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 203,
			'rght' => 204
		),
		array(
			'id' => 104,
			'parent_id' => 102,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add',
			'lft' => 205,
			'rght' => 206
		),
		array(
			'id' => 105,
			'parent_id' => 102,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_edit',
			'lft' => 207,
			'rght' => 208
		),
		array(
			'id' => 106,
			'parent_id' => 102,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 209,
			'rght' => 210
		),
		array(
			'id' => 107,
			'parent_id' => 102,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_moveup',
			'lft' => 211,
			'rght' => 212
		),
		array(
			'id' => 108,
			'parent_id' => 102,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_movedown',
			'lft' => 213,
			'rght' => 214
		),
		array(
			'id' => 109,
			'parent_id' => 102,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_process',
			'lft' => 215,
			'rght' => 216
		),
		array(
			'id' => 110,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'Types',
			'lft' => 218,
			'rght' => 227
		),
		array(
			'id' => 111,
			'parent_id' => 110,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 219,
			'rght' => 220
		),
		array(
			'id' => 112,
			'parent_id' => 110,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add',
			'lft' => 221,
			'rght' => 222
		),
		array(
			'id' => 113,
			'parent_id' => 110,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_edit',
			'lft' => 223,
			'rght' => 224
		),
		array(
			'id' => 114,
			'parent_id' => 110,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 225,
			'rght' => 226
		),
		array(
			'id' => 115,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'Users',
			'lft' => 228,
			'rght' => 261
		),
		array(
			'id' => 116,
			'parent_id' => 115,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 229,
			'rght' => 230
		),
		array(
			'id' => 117,
			'parent_id' => 115,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add',
			'lft' => 231,
			'rght' => 232
		),
		array(
			'id' => 118,
			'parent_id' => 115,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_edit',
			'lft' => 233,
			'rght' => 234
		),
		array(
			'id' => 119,
			'parent_id' => 115,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_reset_password',
			'lft' => 235,
			'rght' => 236
		),
		array(
			'id' => 120,
			'parent_id' => 115,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 237,
			'rght' => 238
		),
		array(
			'id' => 121,
			'parent_id' => 115,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_login',
			'lft' => 239,
			'rght' => 240
		),
		array(
			'id' => 122,
			'parent_id' => 115,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_logout',
			'lft' => 241,
			'rght' => 242
		),
		array(
			'id' => 123,
			'parent_id' => 115,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'index',
			'lft' => 243,
			'rght' => 244
		),
		array(
			'id' => 124,
			'parent_id' => 115,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'add',
			'lft' => 245,
			'rght' => 246
		),
		array(
			'id' => 125,
			'parent_id' => 115,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'activate',
			'lft' => 247,
			'rght' => 248
		),
		array(
			'id' => 126,
			'parent_id' => 115,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'edit',
			'lft' => 249,
			'rght' => 250
		),
		array(
			'id' => 127,
			'parent_id' => 115,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'forgot',
			'lft' => 251,
			'rght' => 252
		),
		array(
			'id' => 128,
			'parent_id' => 115,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'reset',
			'lft' => 253,
			'rght' => 254
		),
		array(
			'id' => 129,
			'parent_id' => 115,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'login',
			'lft' => 255,
			'rght' => 256
		),
		array(
			'id' => 130,
			'parent_id' => 115,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'logout',
			'lft' => 257,
			'rght' => 258
		),
		array(
			'id' => 131,
			'parent_id' => 115,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'view',
			'lft' => 259,
			'rght' => 260
		),
		array(
			'id' => 132,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'Vocabularies',
			'lft' => 262,
			'rght' => 271
		),
		array(
			'id' => 133,
			'parent_id' => 132,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 263,
			'rght' => 264
		),
		array(
			'id' => 134,
			'parent_id' => 132,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add',
			'lft' => 265,
			'rght' => 266
		),
		array(
			'id' => 135,
			'parent_id' => 132,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_edit',
			'lft' => 267,
			'rght' => 268
		),
		array(
			'id' => 136,
			'parent_id' => 132,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 269,
			'rght' => 270
		),
		array(
			'id' => 137,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'AclAcos',
			'lft' => 272,
			'rght' => 281
		),
		array(
			'id' => 138,
			'parent_id' => 137,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 273,
			'rght' => 274
		),
		array(
			'id' => 139,
			'parent_id' => 137,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add',
			'lft' => 275,
			'rght' => 276
		),
		array(
			'id' => 140,
			'parent_id' => 137,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_edit',
			'lft' => 277,
			'rght' => 278
		),
		array(
			'id' => 141,
			'parent_id' => 137,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 279,
			'rght' => 280
		),
		array(
			'id' => 142,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'AclActions',
			'lft' => 282,
			'rght' => 295
		),
		array(
			'id' => 143,
			'parent_id' => 142,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 283,
			'rght' => 284
		),
		array(
			'id' => 144,
			'parent_id' => 142,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add',
			'lft' => 285,
			'rght' => 286
		),
		array(
			'id' => 145,
			'parent_id' => 142,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_edit',
			'lft' => 287,
			'rght' => 288
		),
		array(
			'id' => 146,
			'parent_id' => 142,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 289,
			'rght' => 290
		),
		array(
			'id' => 147,
			'parent_id' => 142,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_move',
			'lft' => 291,
			'rght' => 292
		),
		array(
			'id' => 148,
			'parent_id' => 142,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_generate',
			'lft' => 293,
			'rght' => 294
		),
		array(
			'id' => 149,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'AclAros',
			'lft' => 296,
			'rght' => 305
		),
		array(
			'id' => 150,
			'parent_id' => 149,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 297,
			'rght' => 298
		),
		array(
			'id' => 151,
			'parent_id' => 149,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add',
			'lft' => 299,
			'rght' => 300
		),
		array(
			'id' => 152,
			'parent_id' => 149,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_edit',
			'lft' => 301,
			'rght' => 302
		),
		array(
			'id' => 153,
			'parent_id' => 149,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 303,
			'rght' => 304
		),
		array(
			'id' => 154,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'AclPermissions',
			'lft' => 306,
			'rght' => 311
		),
		array(
			'id' => 155,
			'parent_id' => 154,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 307,
			'rght' => 308
		),
		array(
			'id' => 156,
			'parent_id' => 154,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_toggle',
			'lft' => 309,
			'rght' => 310
		),
		array(
			'id' => 159,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'ExtensionsHooks',
			'lft' => 312,
			'rght' => 317
		),
		array(
			'id' => 160,
			'parent_id' => 159,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 313,
			'rght' => 314
		),
		array(
			'id' => 161,
			'parent_id' => 159,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_toggle',
			'lft' => 315,
			'rght' => 316
		),
		array(
			'id' => 162,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'ExtensionsLocales',
			'lft' => 318,
			'rght' => 329
		),
		array(
			'id' => 163,
			'parent_id' => 162,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 319,
			'rght' => 320
		),
		array(
			'id' => 164,
			'parent_id' => 162,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_activate',
			'lft' => 321,
			'rght' => 322
		),
		array(
			'id' => 165,
			'parent_id' => 162,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add',
			'lft' => 323,
			'rght' => 324
		),
		array(
			'id' => 166,
			'parent_id' => 162,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_edit',
			'lft' => 325,
			'rght' => 326
		),
		array(
			'id' => 167,
			'parent_id' => 162,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 327,
			'rght' => 328
		),
		array(
			'id' => 168,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'ExtensionsPlugins',
			'lft' => 330,
			'rght' => 337
		),
		array(
			'id' => 169,
			'parent_id' => 168,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 331,
			'rght' => 332
		),
		array(
			'id' => 170,
			'parent_id' => 168,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add',
			'lft' => 333,
			'rght' => 334
		),
		array(
			'id' => 171,
			'parent_id' => 168,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 335,
			'rght' => 336
		),
		array(
			'id' => 172,
			'parent_id' => 1,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'ExtensionsThemes',
			'lft' => 338,
			'rght' => 351
		),
		array(
			'id' => 173,
			'parent_id' => 172,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_index',
			'lft' => 339,
			'rght' => 340
		),
		array(
			'id' => 174,
			'parent_id' => 172,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_activate',
			'lft' => 341,
			'rght' => 342
		),
		array(
			'id' => 175,
			'parent_id' => 172,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_add',
			'lft' => 343,
			'rght' => 344
		),
		array(
			'id' => 176,
			'parent_id' => 172,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_editor',
			'lft' => 345,
			'rght' => 346
		),
		array(
			'id' => 177,
			'parent_id' => 172,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_save',
			'lft' => 347,
			'rght' => 348
		),
		array(
			'id' => 178,
			'parent_id' => 172,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'admin_delete',
			'lft' => 349,
			'rght' => 350
		),
	);
}
