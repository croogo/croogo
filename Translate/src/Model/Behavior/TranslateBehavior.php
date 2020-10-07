<?php
declare(strict_types=1);

namespace Croogo\Translate\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
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
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->getStrategy()->getTranslationTable()->addBehavior('Croogo/Core.Trackable');
    }

    /**
     * Delete translations
     */
    public function deleteTranslation(EntityInterface $entity, string $locale)
    {
        $translationTable = $this->getStrategy()->getTranslationTable();
        $runtimeModelAlias = $translationTable->getAlias();
        list(, $targetModel) = pluginSplit($this->_table->getAlias());
        $deleteCond = [
            $runtimeModelAlias . '.model' => $targetModel,
            $runtimeModelAlias . '.foreign_key' => $entity->id,
            $runtimeModelAlias . '.locale' => $locale,
        ];

        return $translationTable->deleteAll($deleteCond);
    }

    /**
     * When missing, populate _locale with default value
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options)
    {
        if (empty($data['_locale'])) {
            $data['_locale'] = I18n::getDefaultLocale();
        }
    }

    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if ($entity->isNew()) {
            return;
        }
        return $this->strategy->beforeSave($event, $entity, $options);
    }

}
