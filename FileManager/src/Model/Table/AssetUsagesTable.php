<?php

namespace Croogo\FileManager\Model\Table;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Croogo\Core\Model\Table\CroogoTable;


/**
 * AssetUsages Table
 *
 */
class AssetUsagesTable extends CroogoTable {

    public function initialize(array $config) {
        parent::initialize($config);
        $this->setTable('asset_usages');

        $this->belongsTo('Assets', [
            'className' => 'Croogo/FileManager.Assets',
            'foreignKey' => 'asset_id',
        ]);

        $this->addBehavior('Croogo/Core.Trackable');
    }

    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options) {
        if (!empty($entity->featured_image)) {
            $entity->type = 'FeaturedImage';
            $entity->unsetProperty('featured_image');
        }
        return true;
    }

}
