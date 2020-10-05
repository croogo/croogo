<?php

namespace Croogo\Install\Model\Table;

use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class InstallTable extends Table
{

    /**
     * name
     *
     * @var string
     */
    public $name = 'Install';

    /**
     * useTable
     *
     * @var string
     */
    public $useTable = false;

    /**
     *
     * @var CroogoPlugin
     */
    protected $_CroogoPlugin = null;

    /**
     * Create admin user
     *
     * @var array $user User datas
     * @return \Cake\Datasource\EntityInterface|false
     */
    public function addAdminUser($user)
    {
        $Users = TableRegistry::get('Croogo/Users.Users');
        $Users->removeBehavior('Cached');
        $Roles = TableRegistry::get('Croogo/Users.Roles');
        $Roles->addBehavior('Croogo/Core.Aliasable');
        $Users->getValidator('default')->remove('email')->remove('password');
        $user['name'] = $user['username'];
        $user['email'] = '';
        $user['timezone'] = 'UTC';
        $user['role_id'] = $Roles->byAlias('superadmin');
        $user['status'] = true;
        $user['activation_key'] = md5(uniqid());
        $entity = $Users->get(1);
        $entity = $Users->patchEntity($entity, $user);
        $errors = $entity->getErrors();
        if ($errors) {
            $field = key($errors);
            $validationErrors = $errors[$field];
            $message = $validationErrors[key($validationErrors)];
            throw new PersistenceFailedException($entity, __d('croogo', 'Unable to create administrative user. Validation errors: %s (%s)', $message, $field));
        }
        $saved = $Users->save($entity);

        return $saved;
    }
}
