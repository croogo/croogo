<?php

namespace Croogo\Users\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Croogo\Core\Auth\DefaultPasswordHasher;

class User extends Entity
{

    protected array $_hidden = ['password', 'activation_key'];

    /**
     * Hashes password when setting
     *
     * @param string $password
     * @return bool|string
     */
    protected function _setPassword($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }

    /**
     * parentNode
     *
     * The ARO of an account always hangs under the ARO of its role. A partial entity - loaded
     * with a narrow select() and saved for one column - carries no role_id; returning null here
     * made AclBehavior::afterSave() re-parent the node to the root (TreeBehavior then moves it to
     * the end of the tree), which silently locked the account out of the whole admin:
     * "Authorization error" on every login. Read the role from the table in that case.
     *
     * @return array|null
     */
    public function parentNode()
    {
        if (!$this->id) {
            return null;
        }
        $roleId = $this->get('role_id');
        if ($roleId === null && !$this->has('role_id')) {
            $roleId = $this->storedRoleId();
        }
        if (empty($roleId)) {
            return null;
        }

        return ['Roles' => ['id' => $roleId]];
    }

    /**
     * role_id as stored for this account - the source of truth when the entity was loaded
     * without that column.
     *
     * @return int|null
     */
    protected function storedRoleId()
    {
        $table = TableRegistry::getTableLocator()->get($this->getSource() ?: 'Croogo/Users.Users');
        $row = $table->find()
            ->select([$table->aliasField('role_id')])
            ->where([$table->aliasField('id') => $this->id])
            ->disableHydration()
            ->first();

        return isset($row['role_id']) ? (int)$row['role_id'] : null;
    }
}
