<?php

namespace Croogo\Acl\Adapter;

use Acl\Adapter\CachedDbAcl;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

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
     * Containing AclComponent
     *
     * @var \Acl\Controller\Component\AclComponent
     */
    public $Acl;

    /**
     * Initializes the containing component and sets the Aro/Aco objects to it.
     *
     * @param AclComponent $component
     * @return void
     */
    public function initialize(Component $component): void
    {
        parent::initialize($component);

        $habtm = $component->getConfig('habtm');
        if (!empty($habtm)) {
            $this->settings = array_merge($this->settings, $habtm);
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
     * @return bool Success (true if ARO has access to action in ACO, false otherwise)
     */
    public function check($aro, $aco, $action = "*")
    {
        if (parent::check($aro, $aco, $action)) {
            return true;
        }
        extract($this->settings);

        $User = \Cake\ORM\TableRegistry::getTableLocator()->get($userModel);
        list($plugin, $groupAlias) = pluginSplit($groupAlias);
        $assoc = $User->associations()->get($groupAlias);
        if (!$assoc instanceof \Cake\ORM\Association\BelongsToMany) {
            return false;
        }

        $joinModel = $assoc->junction();

        $userField = $assoc->getForeignKey();
        $groupField = $assoc->getTargetForeignKey();

        $nodes = $this->Acl->Aro->node($aro);
        $node = $nodes ? $nodes->first() : null;
        // role HABTM dotycza tylko ARO uzytkownika (np. nie 'Role-public',
        // ktorego foreign_key to id roli, nie uzytkownika)
        if (!$node || $node->model !== $User->getAlias() || !$node->foreign_key) {
            return false;
        }
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
