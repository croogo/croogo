<?php

namespace Croogo\Acl;

use Acl\AclExtras;
use Cake\Database\Exception;
use Cake\Datasource\ConnectionInterface;
use Cake\ORM\TableRegistry;
use Croogo\Core\Plugin;

class AclGenerator extends AclExtras
{
    public function __construct()
    {
        $this->Aco = TableRegistry::get('Croogo/Acl.Acos');
        $this->_buildPrefixes();
    }

    public function insertAcos(ConnectionInterface $connection)
    {
        $this->Aco->connection($connection);
        $this->acoUpdate();
    }

    public function out($msg)
    {
        if (!isset($this->Shell)) {
            $msg = preg_replace('/\<\/?\w+\>/', null, $msg);
        }
        return parent::out($msg);
    }

    protected function _checkMethods($className, $controllerName, $node, $pluginPath = null, $prefixPath = null)
    {
        try {
            return parent::_checkMethods($className, $controllerName, $node, $pluginPath, $prefixPath);
        } catch (\Exception $exception) {
        }

        return false;
    }
}
