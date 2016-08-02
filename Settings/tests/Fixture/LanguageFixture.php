<?php

namespace Croogo\Settings\Test\Fixture;

use Croogo\Core\TestSuite\CroogoTestFixture;

class LanguageFixture extends CroogoTestFixture
{

    public $name = 'Language';

    public $fields = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null],
        'title' => ['type' => 'string', 'null' => false, 'default' => null],
        'native' => ['type' => 'string', 'null' => true, 'default' => null],
        'alias' => ['type' => 'string', 'null' => false, 'default' => null],
        'status' => ['type' => 'boolean', 'null' => false, 'default' => '1'],
        'weight' => ['type' => 'integer', 'null' => true, 'default' => null],
        'modified' => ['type' => 'timestamp', 'null' => false, 'default' => null],
        'created' => ['type' => 'timestamp', 'null' => false, 'default' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ],
        '_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB']
    ];

    public $records = [
        [
            'id' => 1,
            'title' => 'English',
            'native' => 'English',
            'alias' => 'eng',
            'status' => 1,
            'weight' => 1,
            'modified' => '2009-11-02 21:37:38',
            'created' => '2009-11-02 20:52:00'
        ],
    ];
}
