<?php

use Cake\ORM\TableRegistry;
use Cake\Log\LogTrait;
use Phinx\Seed\AbstractSeed;

class UsersSeed extends AbstractSeed
{
    use LogTrait;

    public $record = [
        'id' => 1,
        'role_id' => 3,
        'username' => 'seed',
        'name' => 'Seed User',
        'email' => 'seed@example.com',
        'status' => false,
        'timezone' => 'UTC',
    ];

    public function run()
    {
        $Users = TableRegistry::get('Croogo/Users.Users');
        $entity = $Users->newEntity($this->record);
        $result = $Users->save($entity);
    }

}
