<?php

namespace Croogo\Core\Model\Table;

use Cake\Event\Event;
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
     */
    public function __construct(array $config)
    {
        Croogo::applyHookProperties('Hook.table_properties', $this);

        parent::__construct($config);
    }

    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Model.initialize' => 'onModelInitialized',
        ];
    }

    public function onModelInitialized(Event $event)
    {
        foreach ($this->hookedBehaviors as $behavior => $config) {
            $this->addBehavior($behavior, $config);
        }
    }

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
            $this->_editFields = $this->schema()->columns();
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
     *
     * @return bool true when validation successful
     */
    public function validAlias($check)
    {
        return (preg_match('/^[-\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}_]+$/mu', $check[key($check)]) == 1);
    }

    /**
     * Validation method for name or title fields
     *
     * @return bool true when validation successful
     */
    public function validName($check)
    {
        return (preg_match('/^[-\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}_\[\]\(\) ]+$/mu', $check[key($check)]) == 1);
    }
}
