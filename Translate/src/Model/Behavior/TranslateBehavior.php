<?php

namespace Croogo\Translate\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\I18n\I18n;
use Cake\ORM\Behavior\TranslateBehavior as CakeTranslateBehavior;
use Cake\ORM\Table;

class TranslateBehavior extends CakeTranslateBehavior
{

    public function __construct(Table $table, array $config = [])
    {
        $this->_defaultConfig['implementedMethods']['deleteTranslation'] = 'deleteTranslation';

        return parent::__construct($table, $config);
    }

    /**
     * initialize
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->_translationTable->addBehavior('Croogo/Core.Trackable');
    }

    /**
     * Delete translations
     */
    public function deleteTranslation(EntityInterface $entity, string $locale)
    {
        $runtimeModelAlias = $this->_translationTable->getAlias();
        list(, $targetModel) = pluginSplit($this->_table->getAlias());
        $deleteCond = [
            $runtimeModelAlias . '.model' => $targetModel,
            $runtimeModelAlias . '.foreign_key' => $entity->id,
            $runtimeModelAlias . '.locale' => $locale,
        ];

        return $this->_translationTable->deleteAll($deleteCond);
    }

    /**
     * When missing, populate _locale with default value
     */
    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
    {
        if (empty($data['_locale'])) {
            $data['_locale'] = I18n::getDefaultLocale();
        }
    }
}
