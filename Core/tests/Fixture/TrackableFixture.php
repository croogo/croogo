<?php

namespace Croogo\Core\Test\Fixture;

use Croogo\Core\TestSuite\CroogoTestFixture;

class TrackableFixture extends CroogoTestFixture
{

    public $fields = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null],
        'title' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 60],
        'created_by' => ['type' => 'integer', 'null' => false, 'default' => null],
        'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
        'updated_by' => ['type' => 'integer', 'null' => false, 'default' => null],
        'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ],
        '_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
    ];

    public $records = [
    ];
}
