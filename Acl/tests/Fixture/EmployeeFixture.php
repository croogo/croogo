<?php

namespace Croogo\Acl\Test\Fixture;

use Croogo\TestSuite\CroogoTestFixture;

class EmployeeFixture extends CroogoTestFixture
{

/**
 * fields property
 *
 * @var array
 */
    public $fields = [
        'id' => ['type' => 'integer'],
        'username' => ['type' => 'string', 'null' => false],
        'primary_department_id' => ['type' => 'integer', 'null' => true],
        'created' => 'datetime',
        'updated' => 'datetime',
        '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]]
    ];

/**
 * records property
 *
 * @var array
 */
    public $records = [
        ['username' => 'mark', 'created' => '2007-03-17 01:16:23', 'primary_department_id' => 2],
        ['username' => 'jack', 'created' => '2007-03-17 01:18:23', 'primary_department_id' => null],
        ['username' => 'larry', 'created' => '2007-03-17 01:20:23', 'primary_department_id' => null],
        ['username' => 'jose', 'created' => '2007-03-17 01:22:23', 'primary_department_id' => null],
    ];
}
