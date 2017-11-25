<?php

namespace Croogo\Acl\Adapter;

use Acl\Adapter\CachedDbAcl;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * HabtmDbAcl implements an ACL control system in the database like DbAcl with
 * User habtm Group checks
 *
 * @package Croogo.Acl.Controller.Component.Acl
 * @author Ceeram
 * @license MIT
 * @link http://github.com/ceeram/Authorize
 */
class HabtmDbAcl extends CachedDbAcl
{

    public $settings = [
        'userModel' => 'Croogo/Users.Users',
        'groupAlias' => 'Roles',
    ];

/**
 * Initializes the containing component and sets the Aro/Aco objects to it.
 *
 * @param AclComponent $component
 * @return void
 */
    public function initialize(Component $component)
    {
        parent::initialize($component);
        if (!empty($component->settings['habtm'])) {
            $this->settings = array_merge($this->settings, $component->settings['habtm']);
        }
        $this->Acl = $component;
    }

/**
 * Checks if the given $aro has access to action $action in $aco
 * Check returns true once permissions are found, in following order:
 * User node
 * User::parentNode() node
 * Groupnodes of Groups that User has habtm links to
 *
 * @param string $aro ARO The requesting object identifier.
 * @param string $aco ACO The controlled object identifier.
 * @param string $action Action (defaults to *)
 * @return boolean Success (true if ARO has access to action in ACO, false otherwise)
 */
    public function check($aro, $aco, $action = "*")
    {
        if (parent::check($aro, $aco, $action)) {
            return true;
        }
        extract($this->settings);

        $User = TableRegistry::get($userModel);
        list($plugin, $groupAlias) = pluginSplit($groupAlias);
        $assoc = $User->associations()->get($groupAlias);

        $joinModel = $assoc->junction();

        $userField = $assoc->foreignKey();
        $groupField = $assoc->targetForeignKey();

        $node = $this->Acl->Aro->node($aro)->first();
        $userId = $node->foreign_key;
        $query = $joinModel->find()
            ->select([$groupField])
            ->where([$userField => $userId]);
        foreach ($query as $entity) {
            $aro = ['model' => $groupAlias, 'foreign_key' => $entity->get($groupField)];
            $allowed = parent::check($aro, $aco, $action);
            if ($allowed) {
                return true;
            }
        }
        return false;
    }

}
