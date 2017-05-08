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

        $root = $this->_checkNode($this->rootNode, $this->rootNode, null);
        $plugins = Plugin::loaded();
        $this->_processControllers($root);
        $this->_processPrefixes($root);
        $this->_processPlugins($root, $plugins);
    }

    public function out($msg)
    {
        $msg = preg_replace('/\<\/?\w+\>/', null, $msg);
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
