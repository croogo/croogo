<?php

namespace Croogo\Settings\Model\Table;

use ArrayObject;

use Cake\Core\Configure;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Form\Schema;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;
use Croogo\Acl\AclGenerator;
use Croogo\Core\Model\Table\CroogoTable;

/**
 * Setting
 *
 * @category Model
 * @package  Croogo.Settings.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 * @method \Cake\ORM\Query findByKey(string $key)
 */
class SettingsTable extends CroogoTable
{

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notBlank('key', __d('croogo', 'Key cannot be empty.'));
        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules
            ->add($rules->isUnique( ['key'],
                __d('croogo', 'That key is already taken')
            ));
        return $rules;
    }

/**
 * @param array $config
 */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Croogo/Core.Trackable');
//        $this->addBehavior('Croogo/Core.Ordered', [
//            'field' => 'weight',
//            'foreign_key' => false,
//        ]);
        $this->addBehavior('Croogo/Core.Cached', [
            'groups' => [
                'settings',
            ],
        ]);
        $this->addBehavior('Search.Search');

        $this->searchManager()
            ->add('key', 'Search.Like', [
                'after' => true,
                'field' => $this->aliasField('key'),
            ]);
    }

/**
 * @param Table $schema
 * @return Table
 */
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->columnType('params', 'params');
        return $schema;
    }

/**
 * beforeSave callback
 */
    public function beforeSave()
    {
        $this->connection()->driver()->autoQuoting(true);
    }

/**
 * afterSave callback
 */
    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {

        $this->connection()->driver()->autoQuoting(false);
        if ($entity->key == 'Access Control.rowLevel') {
            if ($entity->value == true && $entity->_original['value'] == false) {
                $aclGenerator = new AclGenerator();
                $aclGenerator->syncContentAcos();
            }
        }
    }

/**
 * Creates a new record with key/value pair if key does not exist.
 *
 * @param string $key
 * @param string $value
 * @param array $options
 * @return boolean
 */
    public function write($key, $value, $options = [])
    {
        $setting = $this->findByKey($key)->first();
        if ($setting) {
            $setting->value = $value;
            $setting->type = gettype($value);

            $setting = $this->patchEntity($setting, $options);

        } else {
            $options = array_merge([
                'title' => '',
                'description' => '',
                'input_type' => '',
                'editable' => 0,
                'weight' => 0,
                'params' => '',
                'option_class' => '',
            ], $options);

            $setting = $this->newEntity([
                'key' => $key,
                'value' => $value,
                'type' => gettype($value),
                'title' => $options['title'],
                'description' => $options['description'],
                'input_type' => $options['input_type'],
                'editable' => $options['editable'],
                'weight' => $options['weight'],
                'params' => $options['params'],
                'option_class' => $options['option_class']
            ]);
        }

        if ($this->save($setting)) {
            Configure::write($key, $value);
            return true;
        } else {
            return false;
        }
    }

/**
 * Deletes setting record for given key
 *
 * @param string $key
 * @return boolean
 */
    public function deleteKey($key)
    {
        $setting = $this->findByKey($key)->first();
        if ($setting && $this->delete($setting)) {
            return true;
        }
        return false;
    }
}
