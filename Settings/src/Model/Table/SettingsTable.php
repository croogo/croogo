<?php

namespace Croogo\Settings\Model\Table;

use Cake\Core\Configure;
use Cake\Database\Schema\Table;
use Cake\Form\Schema;
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
/**
 * Validation
 *
 * @var array
 * @access public
 */
    public $validate = [
        'key' => [
            'isUnique' => [
                'rule' => 'isUnique',
                'message' => 'This key has already been taken.',
            ],
            'minLength' => [
                'rule' => ['minLength', 1],
                'message' => 'Key cannot be empty.',
            ],
        ],
    ];

/**
 * Filter search fields
 */
    public $filterArgs = [
        'key' => ['type' => 'like', 'field' => 'Settings.key'],
    ];

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
    }

/**
 * @param Table $schema
 * @return Table
 */
    protected function _initializeSchema(Table $schema)
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
    public function afterSave()
    {
        $this->connection()->driver()->autoQuoting(false);
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

            $setting = $this->patchEntity($setting, $options);

        } else {
            $options = array_merge([
                'title' => '',
                'description' => '',
                'input_type' => '',
                'editable' => 0,
                'weight' => 0,
                'params' => '',
            ], $options);

            $setting = $this->newEntity([
                'key' => $key,
                'value' => $value,
                'title' => $options['title'],
                'description' => $options['description'],
                'input_type' => $options['input_type'],
                'editable' => $options['editable'],
                'weight' => $options['weight'],
                'params' => $options['params'],
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
