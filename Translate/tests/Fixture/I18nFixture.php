<?php

namespace Croogo\Translate\Test\Fixture;

use Croogo\Core\TestSuite\CroogoTestFixture;

class I18nFixture extends CroogoTestFixture
{

    public $name = 'I18n';

    public $fields = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
        'locale' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 6],
        'model' => ['type' => 'string', 'null' => false, 'default' => null],
        'foreign_key' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
        'field' => ['type' => 'string', 'null' => false, 'default' => null],
        'content' => ['type' => 'text', 'null' => true, 'default' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
            'PRIMARY' => ['type' => 'unique', 'columns' => 'id'],
            'locale' => ['type' => 'unique', 'columns' => ['locale', 'model', 'foreign_key', 'field']],
        ]
    ];

    public $table = 'i18n';

    public $records = [
    ];
}
