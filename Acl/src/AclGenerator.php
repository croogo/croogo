<?php

namespace Croogo\Acl;

use Acl\AclExtras;
use Cake\Core\Configure;
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

    public function syncContentAcos()
    {
        $models = Configure::read('Access Control.models');
        if (!$models) {
            return $this->out('No models are configured for row level access control');
        }
        $models = json_decode($models, true);

        $Acos = TableRegistry::get('Croogo/Acl.Acos');
        $parent = $Acos->node('contents')->firstOrFail();
        foreach ($models as $model) {
            $Model = TableRegistry::get($model);
            $rows = $Model->find()
                ->select('id')
                ->all();
            foreach ($rows as $row) {
                try {
                    $node = $Acos->node($row);
                } catch (\Exception $e) {
                    $aco = $Acos->newEntity([
                        'model' => $Model->alias(),
                        'foreign_key' => $row->id,
                        'alias' => sprintf('%s.%s', $Model->alias(), $row->id),
                        'parent_id' => $parent->id,
                    ]);
                    $saved = $Acos->save($aco);
                }
            }
        }
    }

}
