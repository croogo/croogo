<?php

use Cake\Log\LogTrait;
use Cake\ORM\TableRegistry;
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
        'created_by' => 1,
    ];

    public function getDependencies(): array
    {
        return [
            'RolesSeed',
        ];
    }

    public function run(): void
    {
        $this->getAdapter()->commitTransaction();
        $Users = TableRegistry::getTableLocator()->get('Croogo/Users.Users');
        $entity = $Users->newEntity($this->record);
        $result = $Users->save($entity);
        $this->getAdapter()->beginTransaction();
    }
}
