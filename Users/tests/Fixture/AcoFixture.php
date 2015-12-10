<?php

namespace Croogo\Users\Test\Fixture;

use Croogo\Core\TestSuite\CroogoTestFixture;

class AcoFixture extends CroogoTestFixture
{

    public $name = 'Aco';

    public $fields = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null],
        'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
        'model' => ['type' => 'string', 'null' => true],
        'foreign_key' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
        'alias' => ['type' => 'string', 'null' => true],
        'lft' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
        'rght' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ],
        '_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
    ];

    public $records = [
        [
            'id' => 1,
            'parent_id' => null,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'controllers',
            'lft' => 1,
            'rght' => 352
        ],
        [
            'id' => 2,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'Attachments',
            'lft' => 2,
            'rght' => 13
        ],
        [
            'id' => 3,
            'parent_id' => 2,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 3,
            'rght' => 4
        ],
        [
            'id' => 4,
            'parent_id' => 2,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add',
            'lft' => 5,
            'rght' => 6
        ],
        [
            'id' => 5,
            'parent_id' => 2,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_edit',
            'lft' => 7,
            'rght' => 8
        ],
        [
            'id' => 6,
            'parent_id' => 2,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 9,
            'rght' => 10
        ],
        [
            'id' => 7,
            'parent_id' => 2,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_browse',
            'lft' => 11,
            'rght' => 12
        ],
        [
            'id' => 8,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'Blocks',
            'lft' => 14,
            'rght' => 29
        ],
        [
            'id' => 9,
            'parent_id' => 8,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 15,
            'rght' => 16
        ],
        [
            'id' => 10,
            'parent_id' => 8,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add',
            'lft' => 17,
            'rght' => 18
        ],
        [
            'id' => 11,
            'parent_id' => 8,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_edit',
            'lft' => 19,
            'rght' => 20
        ],
        [
            'id' => 12,
            'parent_id' => 8,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 21,
            'rght' => 22
        ],
        [
            'id' => 13,
            'parent_id' => 8,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_moveup',
            'lft' => 23,
            'rght' => 24
        ],
        [
            'id' => 14,
            'parent_id' => 8,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_movedown',
            'lft' => 25,
            'rght' => 26
        ],
        [
            'id' => 15,
            'parent_id' => 8,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_process',
            'lft' => 27,
            'rght' => 28
        ],
        [
            'id' => 16,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'Comments',
            'lft' => 30,
            'rght' => 45
        ],
        [
            'id' => 17,
            'parent_id' => 16,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 31,
            'rght' => 32
        ],
        [
            'id' => 18,
            'parent_id' => 16,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_edit',
            'lft' => 33,
            'rght' => 34
        ],
        [
            'id' => 19,
            'parent_id' => 16,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 35,
            'rght' => 36
        ],
        [
            'id' => 20,
            'parent_id' => 16,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_process',
            'lft' => 37,
            'rght' => 38
        ],
        [
            'id' => 21,
            'parent_id' => 16,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'index',
            'lft' => 39,
            'rght' => 40
        ],
        [
            'id' => 22,
            'parent_id' => 16,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'add',
            'lft' => 41,
            'rght' => 42
        ],
        [
            'id' => 23,
            'parent_id' => 16,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'delete',
            'lft' => 43,
            'rght' => 44
        ],
        [
            'id' => 24,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'Contacts',
            'lft' => 46,
            'rght' => 57
        ],
        [
            'id' => 25,
            'parent_id' => 24,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 47,
            'rght' => 48
        ],
        [
            'id' => 26,
            'parent_id' => 24,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add',
            'lft' => 49,
            'rght' => 50
        ],
        [
            'id' => 27,
            'parent_id' => 24,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_edit',
            'lft' => 51,
            'rght' => 52
        ],
        [
            'id' => 28,
            'parent_id' => 24,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 53,
            'rght' => 54
        ],
        [
            'id' => 29,
            'parent_id' => 24,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'view',
            'lft' => 55,
            'rght' => 56
        ],
        [
            'id' => 30,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'Filemanager',
            'lft' => 58,
            'rght' => 79
        ],
        [
            'id' => 31,
            'parent_id' => 30,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 59,
            'rght' => 60
        ],
        [
            'id' => 32,
            'parent_id' => 30,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_browse',
            'lft' => 61,
            'rght' => 62
        ],
        [
            'id' => 33,
            'parent_id' => 30,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_editfile',
            'lft' => 63,
            'rght' => 64
        ],
        [
            'id' => 34,
            'parent_id' => 30,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_upload',
            'lft' => 65,
            'rght' => 66
        ],
        [
            'id' => 35,
            'parent_id' => 30,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete_file',
            'lft' => 67,
            'rght' => 68
        ],
        [
            'id' => 36,
            'parent_id' => 30,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete_directory',
            'lft' => 69,
            'rght' => 70
        ],
        [
            'id' => 37,
            'parent_id' => 30,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_rename',
            'lft' => 71,
            'rght' => 72
        ],
        [
            'id' => 38,
            'parent_id' => 30,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_create_directory',
            'lft' => 73,
            'rght' => 74
        ],
        [
            'id' => 39,
            'parent_id' => 30,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_create_file',
            'lft' => 75,
            'rght' => 76
        ],
        [
            'id' => 40,
            'parent_id' => 30,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_chmod',
            'lft' => 77,
            'rght' => 78
        ],
        [
            'id' => 41,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'Languages',
            'lft' => 80,
            'rght' => 95
        ],
        [
            'id' => 42,
            'parent_id' => 41,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 81,
            'rght' => 82
        ],
        [
            'id' => 43,
            'parent_id' => 41,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add',
            'lft' => 83,
            'rght' => 84
        ],
        [
            'id' => 44,
            'parent_id' => 41,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_edit',
            'lft' => 85,
            'rght' => 86
        ],
        [
            'id' => 45,
            'parent_id' => 41,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 87,
            'rght' => 88
        ],
        [
            'id' => 46,
            'parent_id' => 41,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_moveup',
            'lft' => 89,
            'rght' => 90
        ],
        [
            'id' => 47,
            'parent_id' => 41,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_movedown',
            'lft' => 91,
            'rght' => 92
        ],
        [
            'id' => 48,
            'parent_id' => 41,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_select',
            'lft' => 93,
            'rght' => 94
        ],
        [
            'id' => 49,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'Links',
            'lft' => 96,
            'rght' => 111
        ],
        [
            'id' => 50,
            'parent_id' => 49,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 97,
            'rght' => 98
        ],
        [
            'id' => 51,
            'parent_id' => 49,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add',
            'lft' => 99,
            'rght' => 100
        ],
        [
            'id' => 52,
            'parent_id' => 49,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_edit',
            'lft' => 101,
            'rght' => 102
        ],
        [
            'id' => 53,
            'parent_id' => 49,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 103,
            'rght' => 104
        ],
        [
            'id' => 54,
            'parent_id' => 49,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_moveup',
            'lft' => 105,
            'rght' => 106
        ],
        [
            'id' => 55,
            'parent_id' => 49,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_movedown',
            'lft' => 107,
            'rght' => 108
        ],
        [
            'id' => 56,
            'parent_id' => 49,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_process',
            'lft' => 109,
            'rght' => 110
        ],
        [
            'id' => 57,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'Menus',
            'lft' => 112,
            'rght' => 121
        ],
        [
            'id' => 58,
            'parent_id' => 57,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 113,
            'rght' => 114
        ],
        [
            'id' => 59,
            'parent_id' => 57,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add',
            'lft' => 115,
            'rght' => 116
        ],
        [
            'id' => 60,
            'parent_id' => 57,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_edit',
            'lft' => 117,
            'rght' => 118
        ],
        [
            'id' => 61,
            'parent_id' => 57,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 119,
            'rght' => 120
        ],
        [
            'id' => 62,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'Messages',
            'lft' => 122,
            'rght' => 131
        ],
        [
            'id' => 63,
            'parent_id' => 62,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 123,
            'rght' => 124
        ],
        [
            'id' => 64,
            'parent_id' => 62,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_edit',
            'lft' => 125,
            'rght' => 126
        ],
        [
            'id' => 65,
            'parent_id' => 62,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 127,
            'rght' => 128
        ],
        [
            'id' => 66,
            'parent_id' => 62,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_process',
            'lft' => 129,
            'rght' => 130
        ],
        [
            'id' => 67,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'Nodes',
            'lft' => 132,
            'rght' => 161
        ],
        [
            'id' => 68,
            'parent_id' => 67,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 133,
            'rght' => 134
        ],
        [
            'id' => 69,
            'parent_id' => 67,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_create',
            'lft' => 135,
            'rght' => 136
        ],
        [
            'id' => 70,
            'parent_id' => 67,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add',
            'lft' => 137,
            'rght' => 138
        ],
        [
            'id' => 71,
            'parent_id' => 67,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_edit',
            'lft' => 139,
            'rght' => 140
        ],
        [
            'id' => 72,
            'parent_id' => 67,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_update_paths',
            'lft' => 141,
            'rght' => 142
        ],
        [
            'id' => 73,
            'parent_id' => 67,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 143,
            'rght' => 144
        ],
        [
            'id' => 74,
            'parent_id' => 67,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete_meta',
            'lft' => 145,
            'rght' => 146
        ],
        [
            'id' => 75,
            'parent_id' => 67,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add_meta',
            'lft' => 147,
            'rght' => 148
        ],
        [
            'id' => 76,
            'parent_id' => 67,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_process',
            'lft' => 149,
            'rght' => 150
        ],
        [
            'id' => 77,
            'parent_id' => 67,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'index',
            'lft' => 151,
            'rght' => 152
        ],
        [
            'id' => 78,
            'parent_id' => 67,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'term',
            'lft' => 153,
            'rght' => 154
        ],
        [
            'id' => 79,
            'parent_id' => 67,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'promoted',
            'lft' => 155,
            'rght' => 156
        ],
        [
            'id' => 80,
            'parent_id' => 67,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'search',
            'lft' => 157,
            'rght' => 158
        ],
        [
            'id' => 81,
            'parent_id' => 67,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'view',
            'lft' => 159,
            'rght' => 160
        ],
        [
            'id' => 82,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'Regions',
            'lft' => 162,
            'rght' => 171
        ],
        [
            'id' => 83,
            'parent_id' => 82,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 163,
            'rght' => 164
        ],
        [
            'id' => 84,
            'parent_id' => 82,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add',
            'lft' => 165,
            'rght' => 166
        ],
        [
            'id' => 85,
            'parent_id' => 82,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_edit',
            'lft' => 167,
            'rght' => 168
        ],
        [
            'id' => 86,
            'parent_id' => 82,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 169,
            'rght' => 170
        ],
        [
            'id' => 87,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'Roles',
            'lft' => 172,
            'rght' => 181
        ],
        [
            'id' => 88,
            'parent_id' => 87,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 173,
            'rght' => 174
        ],
        [
            'id' => 89,
            'parent_id' => 87,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add',
            'lft' => 175,
            'rght' => 176
        ],
        [
            'id' => 90,
            'parent_id' => 87,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_edit',
            'lft' => 177,
            'rght' => 178
        ],
        [
            'id' => 91,
            'parent_id' => 87,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 179,
            'rght' => 180
        ],
        [
            'id' => 92,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'Settings',
            'lft' => 182,
            'rght' => 201
        ],
        [
            'id' => 93,
            'parent_id' => 92,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_dashboard',
            'lft' => 183,
            'rght' => 184
        ],
        [
            'id' => 94,
            'parent_id' => 92,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 185,
            'rght' => 186
        ],
        [
            'id' => 95,
            'parent_id' => 92,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_view',
            'lft' => 187,
            'rght' => 188
        ],
        [
            'id' => 96,
            'parent_id' => 92,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add',
            'lft' => 189,
            'rght' => 190
        ],
        [
            'id' => 97,
            'parent_id' => 92,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_edit',
            'lft' => 191,
            'rght' => 192
        ],
        [
            'id' => 98,
            'parent_id' => 92,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 193,
            'rght' => 194
        ],
        [
            'id' => 99,
            'parent_id' => 92,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_prefix',
            'lft' => 195,
            'rght' => 196
        ],
        [
            'id' => 100,
            'parent_id' => 92,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_moveup',
            'lft' => 197,
            'rght' => 198
        ],
        [
            'id' => 101,
            'parent_id' => 92,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_movedown',
            'lft' => 199,
            'rght' => 200
        ],
        [
            'id' => 102,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'Terms',
            'lft' => 202,
            'rght' => 217
        ],
        [
            'id' => 103,
            'parent_id' => 102,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 203,
            'rght' => 204
        ],
        [
            'id' => 104,
            'parent_id' => 102,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add',
            'lft' => 205,
            'rght' => 206
        ],
        [
            'id' => 105,
            'parent_id' => 102,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_edit',
            'lft' => 207,
            'rght' => 208
        ],
        [
            'id' => 106,
            'parent_id' => 102,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 209,
            'rght' => 210
        ],
        [
            'id' => 107,
            'parent_id' => 102,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_moveup',
            'lft' => 211,
            'rght' => 212
        ],
        [
            'id' => 108,
            'parent_id' => 102,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_movedown',
            'lft' => 213,
            'rght' => 214
        ],
        [
            'id' => 109,
            'parent_id' => 102,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_process',
            'lft' => 215,
            'rght' => 216
        ],
        [
            'id' => 110,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'Types',
            'lft' => 218,
            'rght' => 227
        ],
        [
            'id' => 111,
            'parent_id' => 110,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 219,
            'rght' => 220
        ],
        [
            'id' => 112,
            'parent_id' => 110,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add',
            'lft' => 221,
            'rght' => 222
        ],
        [
            'id' => 113,
            'parent_id' => 110,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_edit',
            'lft' => 223,
            'rght' => 224
        ],
        [
            'id' => 114,
            'parent_id' => 110,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 225,
            'rght' => 226
        ],
        [
            'id' => 115,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'Users',
            'lft' => 228,
            'rght' => 261
        ],
        [
            'id' => 116,
            'parent_id' => 115,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 229,
            'rght' => 230
        ],
        [
            'id' => 117,
            'parent_id' => 115,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add',
            'lft' => 231,
            'rght' => 232
        ],
        [
            'id' => 118,
            'parent_id' => 115,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_edit',
            'lft' => 233,
            'rght' => 234
        ],
        [
            'id' => 119,
            'parent_id' => 115,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_reset_password',
            'lft' => 235,
            'rght' => 236
        ],
        [
            'id' => 120,
            'parent_id' => 115,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 237,
            'rght' => 238
        ],
        [
            'id' => 121,
            'parent_id' => 115,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_login',
            'lft' => 239,
            'rght' => 240
        ],
        [
            'id' => 122,
            'parent_id' => 115,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_logout',
            'lft' => 241,
            'rght' => 242
        ],
        [
            'id' => 123,
            'parent_id' => 115,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'index',
            'lft' => 243,
            'rght' => 244
        ],
        [
            'id' => 124,
            'parent_id' => 115,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'add',
            'lft' => 245,
            'rght' => 246
        ],
        [
            'id' => 125,
            'parent_id' => 115,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'activate',
            'lft' => 247,
            'rght' => 248
        ],
        [
            'id' => 126,
            'parent_id' => 115,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'edit',
            'lft' => 249,
            'rght' => 250
        ],
        [
            'id' => 127,
            'parent_id' => 115,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'forgot',
            'lft' => 251,
            'rght' => 252
        ],
        [
            'id' => 128,
            'parent_id' => 115,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'reset',
            'lft' => 253,
            'rght' => 254
        ],
        [
            'id' => 129,
            'parent_id' => 115,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'login',
            'lft' => 255,
            'rght' => 256
        ],
        [
            'id' => 130,
            'parent_id' => 115,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'logout',
            'lft' => 257,
            'rght' => 258
        ],
        [
            'id' => 131,
            'parent_id' => 115,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'view',
            'lft' => 259,
            'rght' => 260
        ],
        [
            'id' => 132,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'Vocabularies',
            'lft' => 262,
            'rght' => 271
        ],
        [
            'id' => 133,
            'parent_id' => 132,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 263,
            'rght' => 264
        ],
        [
            'id' => 134,
            'parent_id' => 132,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add',
            'lft' => 265,
            'rght' => 266
        ],
        [
            'id' => 135,
            'parent_id' => 132,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_edit',
            'lft' => 267,
            'rght' => 268
        ],
        [
            'id' => 136,
            'parent_id' => 132,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 269,
            'rght' => 270
        ],
        [
            'id' => 137,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'AclAcos',
            'lft' => 272,
            'rght' => 281
        ],
        [
            'id' => 138,
            'parent_id' => 137,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 273,
            'rght' => 274
        ],
        [
            'id' => 139,
            'parent_id' => 137,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add',
            'lft' => 275,
            'rght' => 276
        ],
        [
            'id' => 140,
            'parent_id' => 137,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_edit',
            'lft' => 277,
            'rght' => 278
        ],
        [
            'id' => 141,
            'parent_id' => 137,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 279,
            'rght' => 280
        ],
        [
            'id' => 142,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'AclActions',
            'lft' => 282,
            'rght' => 295
        ],
        [
            'id' => 143,
            'parent_id' => 142,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 283,
            'rght' => 284
        ],
        [
            'id' => 144,
            'parent_id' => 142,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add',
            'lft' => 285,
            'rght' => 286
        ],
        [
            'id' => 145,
            'parent_id' => 142,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_edit',
            'lft' => 287,
            'rght' => 288
        ],
        [
            'id' => 146,
            'parent_id' => 142,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 289,
            'rght' => 290
        ],
        [
            'id' => 147,
            'parent_id' => 142,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_move',
            'lft' => 291,
            'rght' => 292
        ],
        [
            'id' => 148,
            'parent_id' => 142,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_generate',
            'lft' => 293,
            'rght' => 294
        ],
        [
            'id' => 149,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'AclAros',
            'lft' => 296,
            'rght' => 305
        ],
        [
            'id' => 150,
            'parent_id' => 149,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 297,
            'rght' => 298
        ],
        [
            'id' => 151,
            'parent_id' => 149,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add',
            'lft' => 299,
            'rght' => 300
        ],
        [
            'id' => 152,
            'parent_id' => 149,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_edit',
            'lft' => 301,
            'rght' => 302
        ],
        [
            'id' => 153,
            'parent_id' => 149,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 303,
            'rght' => 304
        ],
        [
            'id' => 154,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'AclPermissions',
            'lft' => 306,
            'rght' => 311
        ],
        [
            'id' => 155,
            'parent_id' => 154,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 307,
            'rght' => 308
        ],
        [
            'id' => 156,
            'parent_id' => 154,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_toggle',
            'lft' => 309,
            'rght' => 310
        ],
        [
            'id' => 159,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'ExtensionsHooks',
            'lft' => 312,
            'rght' => 317
        ],
        [
            'id' => 160,
            'parent_id' => 159,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 313,
            'rght' => 314
        ],
        [
            'id' => 161,
            'parent_id' => 159,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_toggle',
            'lft' => 315,
            'rght' => 316
        ],
        [
            'id' => 162,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'ExtensionsLocales',
            'lft' => 318,
            'rght' => 329
        ],
        [
            'id' => 163,
            'parent_id' => 162,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 319,
            'rght' => 320
        ],
        [
            'id' => 164,
            'parent_id' => 162,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_activate',
            'lft' => 321,
            'rght' => 322
        ],
        [
            'id' => 165,
            'parent_id' => 162,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add',
            'lft' => 323,
            'rght' => 324
        ],
        [
            'id' => 166,
            'parent_id' => 162,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_edit',
            'lft' => 325,
            'rght' => 326
        ],
        [
            'id' => 167,
            'parent_id' => 162,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 327,
            'rght' => 328
        ],
        [
            'id' => 168,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'ExtensionsPlugins',
            'lft' => 330,
            'rght' => 337
        ],
        [
            'id' => 169,
            'parent_id' => 168,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 331,
            'rght' => 332
        ],
        [
            'id' => 170,
            'parent_id' => 168,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add',
            'lft' => 333,
            'rght' => 334
        ],
        [
            'id' => 171,
            'parent_id' => 168,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 335,
            'rght' => 336
        ],
        [
            'id' => 172,
            'parent_id' => 1,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'ExtensionsThemes',
            'lft' => 338,
            'rght' => 351
        ],
        [
            'id' => 173,
            'parent_id' => 172,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_index',
            'lft' => 339,
            'rght' => 340
        ],
        [
            'id' => 174,
            'parent_id' => 172,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_activate',
            'lft' => 341,
            'rght' => 342
        ],
        [
            'id' => 175,
            'parent_id' => 172,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_add',
            'lft' => 343,
            'rght' => 344
        ],
        [
            'id' => 176,
            'parent_id' => 172,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_editor',
            'lft' => 345,
            'rght' => 346
        ],
        [
            'id' => 177,
            'parent_id' => 172,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_save',
            'lft' => 347,
            'rght' => 348
        ],
        [
            'id' => 178,
            'parent_id' => 172,
            'model' => null,
            'foreign_key' => null,
            'alias' => 'admin_delete',
            'lft' => 349,
            'rght' => 350
        ],
    ];
}
