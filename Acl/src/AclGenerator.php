<?php

namespace Croogo\Acl;

use Acl\AclExtras;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionInterface;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * Class AclGenerator
 */
class AclGenerator extends AclExtras
{
    /**
     * AclGenerator constructor.
     */
    public function __construct()
    {
        $this->Aco = TableRegistry::get('Croogo/Acl.Acos');
        $this->_buildPrefixes();
    }

    /**
     * @param ConnectionInterface $connection
     * @return void
     */
    public function insertAcos(ConnectionInterface $connection)
    {
        $this->Aco->setConnection($connection);
        $this->acoUpdate();
    }

    /**
     * @param string $msg
     * @return string|void
     */
    public function out($msg)
    {
        if (!isset($this->Shell)) {
            $msg = preg_replace('/\<\/?\w+\>/', null, $msg);
        }

        if (isset($this->Shell) || isset($this->controller)) {
            return parent::out($msg);
        } else {
            \Cake\Log\Log::warning($msg);
        }
    }

    /**
     * @param string $className
     * @param string $controllerName
     * @param array $node
     * @param null $pluginPath
     * @param null $prefixPath
     *
     * @return bool
     */
    protected function _checkMethods($className, $controllerName, $node, $pluginPath = null, $prefixPath = null)
    {
        try {
            return parent::_checkMethods($className, $controllerName, $node, $pluginPath, $prefixPath);
        } catch (\Exception $exception) {
        }

        return false;
    }

    /**
     * @return void
     */
    public function syncContentAcos()
    {
        $models = Configure::read('Access Control.models');
        if (!$models) {
            $message = 'No models are configured for row level access control';
            $this->out($message);
        }
        $models = json_decode($models, true);

        $Acos = TableRegistry::get('Croogo/Acl.Acos');
        $query = $Acos->node('contents');
        if ($query) {
            $parent = $query->first();
        } else {
            $entity = $Acos->newEntity([
                'parent_id' => null,
                'alias' => 'contents',
            ]);
            $parent = $Acos->save($entity);
        }
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
