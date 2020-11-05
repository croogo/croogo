<?php
declare(strict_types=1);

namespace Croogo\Translate\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * I18n Model
 *
 * @method \Croogo\Translate\Model\Entity\I18n newEmptyEntity()
 * @method \Croogo\Translate\Model\Entity\I18n newEntity(array $data, array $options = [])
 * @method \Croogo\Translate\Model\Entity\I18n[] newEntities(array $data, array $options = [])
 * @method \Croogo\Translate\Model\Entity\I18n get($primaryKey, $options = [])
 * @method \Croogo\Translate\Model\Entity\I18n findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \Croogo\Translate\Model\Entity\I18n patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Croogo\Translate\Model\Entity\I18n[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \Croogo\Translate\Model\Entity\I18n|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Croogo\Translate\Model\Entity\I18n saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Croogo\Translate\Model\Entity\I18n[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \Croogo\Translate\Model\Entity\I18n[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \Croogo\Translate\Model\Entity\I18n[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \Croogo\Translate\Model\Entity\I18n[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @property \Croogo\Users\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $TrackableCreator
 * @property \Croogo\Users\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $TrackableUpdater
 * @mixin \Croogo\Core\Model\Behavior\TrackableBehavior
 */
class I18nTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        // \cake\log\log::error(print_r(\Cake\Error\Debugger::trace(), true));
        //die('wtf');
        parent::initialize($config);

        $this->setTable('i18n');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('locale')
            ->maxLength('locale', 5)
            ->requirePresence('locale', 'create')
            ->notEmptyString('locale');

        $validator
            ->scalar('model')
            ->maxLength('model', 200)
            ->requirePresence('model', 'create')
            ->notEmptyString('model');

        $validator
            ->integer('foreign_key')
            ->requirePresence('foreign_key', 'create')
            ->notEmptyString('foreign_key');

        $validator
            ->scalar('field')
            ->maxLength('field', 255)
            ->requirePresence('field', 'create')
            ->notEmptyString('field');

        $validator
            ->scalar('content')
            ->allowEmptyString('content');

        $validator
            ->integer('created_by')
            ->requirePresence('created_by', 'create')
            ->notEmptyString('created_by');

        $validator
            ->integer('modified_by')
            ->allowEmptyString('modified_by');

        return $validator;
    }
}
