<?php

namespace Croogo\Core\Model\Table;

use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Croogo\Core\Croogo;
use Croogo\Core\PropertyHookTrait;

/**
 * Croogo Base Table class
 *
 * @category Croogo.Model
 * @package  Croogo.Croogo.Model.Table
 * @version  1.5
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoTable extends Table
{

    use PropertyHookTrait;

/**
 * use Caching
 *
 * @var string
 */
    public $useCache = true;

/**
 * Default behaviors
 */
    public $actsAs = [
        'Containable',
    ];

    public $hookedBehaviors = [];

/**
 * Display fields for admin_index. Use displayFields()
 *
 * @var array
 * @access protected
 */
    protected $_displayFields = [];

/**
 * Edit fields for admin_edit. Use editFields()
 *
 * @var array
 * @access protected
 */
    protected $_editFields = [];

/**
 * Constructor
 *
 * @param mixed  $id Set this ID for this model on startup, can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 */
    public function __construct($id = false, $table = null, $ds = null)
    {
        Croogo::applyHookProperties('Hook.table_properties', $this);

        parent::__construct($id, $table, $ds);
    }

    public function initialize(array $config)
    {
        parent::initialize($config);

        foreach ($this->hookedBehaviors as $behavior => $config) {
            $this->behaviors()->load($behavior, $config);
        }
    }

/**
 * Updates multiple model records based on a set of conditions.
 *
 * call afterSave() callback after successful update.
 *
 * @param array $fields  Set of fields and values, indexed by fields.
 *     Fields are treated as SQL snippets, to insert literal values manually escape your data.
 * @param mixed $conditions Conditions to match, true for all records
 * @return boolean True on success, false on failure
 * @access public
 */
//	public function updateAll($fields, $conditions = true) {
//		$args = func_get_args();
//		$output = call_user_func_array(array('parent', 'updateAll'), $args);
//		if ($output) {
//			$created = false;
//			$options = array();
//			$field = sprintf('%s.%s', $this->alias(), $this->primaryKey);
//			if (!empty($args[1][$field])) {
//				foreach ((array)$args[1][$field] as $id) {
//					$this->id = $id;
//					$event = new Event('Model.afterSave', $this, array(
//						$created, $options
//					));
//					$this->eventManager()->dispatch($event);
//				}
//			}
//			$this->_clearCache();
//			return true;
//		}
//		return false;
//	}

/**
 * Fix to the Model::invalidate() method to display localized validate messages
 *
 * @param string $field The name of the field to invalidate
 * @param mixed $value Name of validation rule that was not failed, or validation message to
 *  be returned. If no validation key is provided, defaults to true.
 * @access public
 */
    public function invalidate($field, $value = true)
    {
        return parent::invalidate($field, __d('croogo', $value));
    }

/**
 * Return formatted display fields
 *
 * @param array $displayFields
 * @return array
 */
    public function displayFields($displayFields = null)
    {
        if (isset($displayFields)) {
            $this->_displayFields = $displayFields;
        }
        $out = [];
        $defaults = ['sort' => true, 'type' => 'text', 'url' => [], 'options' => []];
        foreach ($this->_displayFields as $field => $label) {
            if (is_int($field)) {
                $field = $label;
                list(, $label) = pluginSplit($label);
                $out[$field] = Hash::merge($defaults, [
                    'label' => Inflector::humanize($label),
                ]);
            } elseif (is_array($label)) {
                $out[$field] = Hash::merge($defaults, $label);
                if (!isset($out[$field]['label'])) {
                    $out[$field]['label'] = Inflector::humanize($field);
                }
            } else {
                $out[$field] = Hash::merge($defaults, [
                    'label' => $label,
                ]);
            }
        }
        return $out;
    }

/**
 * Return formatted edit fields
 *
 * @param array $editFields
 * @return array
 */
    public function editFields($editFields = null)
    {
        if (isset($editFields)) {
            $this->_editFields = $editFields;
        }
        if (empty($this->_editFields)) {
            $this->_editFields = array_keys($this->schema());
            $id = array_search('id', $this->_editFields);
            if ($id !== false) {
                unset($this->_editFields[$id]);
            }
        }
        $out = [];
        foreach ($this->_editFields as $field => $label) {
            if (is_int($field)) {
                $out[$label] = [];
            } elseif (is_array($label)) {
                $out[$field] = $label;
            } else {
                $out[$field] = [
                    'label' => $label,
                ];
            }
        }
        return $out;
    }

/**
 * Validation method for alias field
 * @return bool true when validation successful
 * @deprecated Protected validation methods are no longer supported
 */
    protected function _validAlias($check)
    {
        return $this->validAlias($check);
    }

/**
 * Validation method for name or title fields
 * @return bool true when validation successful
 * @deprecated Protected validation methods are no longer supported
 */
    protected function _validName($check)
    {
        return $this->validName($check);
    }

/**
 * Validation method for alias field
 *
 * @return bool true when validation successful
 */
    public function validAlias($check)
    {
        return (preg_match('/^[\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}-_]+$/mu', $check[key($check)]) == 1);
    }

/**
 * Validation method for name or title fields
 *
 * @return bool true when validation successful
 */
    public function validName($check)
    {
        return (preg_match('/^[\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}-_\[\]\(\) ]+$/mu', $check[key($check)]) == 1);
    }
}
