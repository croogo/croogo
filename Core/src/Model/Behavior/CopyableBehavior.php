<?php

namespace Croogo\Core\Model\Behavior;

use Cake\Datasource\EntityInterface;
use Cake\Log\Log;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Croogo\Core\Croogo;
use PDOException;

/**
 * Copyable Behavior class file.
 *
 * Adds ability to copy a model record, including all hasMany and
 * hasAndBelongsToMany associations. Relies on Containable behavior, which
 * this behavior will attach on the fly as needed.
 *
 * HABTM relationships are just duplicated in the join table, while hasMany
 * and hasOne records are recursively copied as well.
 *
 * Usage is straightforward:
 * From model: $this->copy($id); // id = the id of the record to be copied
 * From container: $this->MyModel->copy($id);
 *
 * @category Behavior
 * @package Croogo.Croogo.Model.Behavior
 * @author Jamie Nay
 * @copyright Jamie Nay
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link http://github.com/jamienay/copyable_behavior
 * @link http://www.croogo.org
 */
class CopyableBehavior extends Behavior
{

    /**
     * Behavior settings
     */
    public $settings = [];

    /**
     * Array of contained models.
     */
    public $contain = [];

    /**
     * The full results of Model::find() that are modified and saved
     * as a new copy.
     */
    public $record;

    /**
     * Default values for settings.
     *
     * - recursive: whether to copy hasMany and hasOne records
     * - habtm: whether to copy hasAndBelongsToMany associations
     * - stripFields: fields to strip during copy process
     * - ignore: aliases of any associations that should be ignored, using dot (.) notation.
     * will look in the $this->contain array.
     */
    protected $_defaults = [
        'recursive' => false,
        'habtm' => false,
        'autoFields' => [
            'title',
            'slug',
            'alias',
        ],
        'stripFields' => [
            'id',
            'created',
            'modified',
            'updated',
            'lft',
            'status',
            'rght'
        ],
        'ignore' => [
        ],
        'masterKey' => null
    ];

    /**
     * Holds the value of the original id
     */
    protected $_originalId;

    /**
     * Constructor
     */
    public function __construct(Table $table, array $config = [])
    {
        $config = Hash::merge($this->_defaults, $config);
        parent::__construct($table, $config);
    }

    /**
     * Copy method.
     *
     * @param Table $id model object
     * @return bool
     */
    public function copy($id)
    {
        $this->_originalId = $id;
        $table = $this->_table;
        $this->contain = $this->generateContain();
        $this->record = $table->find()->where([
                $table->aliasField($table->getPrimaryKey()) => $id
            ])
            ->contain($this->contain)
            ->first();

        if (empty($this->record)) {
            return false;
        }

        if (!$this->_convertData()) {
            return false;
        }

        $result = false;
        try {
            $result = $this->_table->save($this->record, [
                'associated' => true,
            ]);
        } catch (PDOException $e) {
            Log:error('Error executing _copyRecord: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Wrapper method that combines the results of _recursiveChildContain()
     * with the models' HABTM associations.
     *
     * @return array
     */
    public function generateContain()
    {
        $contain = [];
        $table = $this->_table;
        $belongsToMany = $table->associations()->getByType('BelongsToMany');
        foreach ($belongsToMany as $assoc) {
            $contain[$assoc->junction()->getAlias()] = [];
        }
        $contain = array_merge($this->_recursiveChildContain($table), $contain);
        $contain = $this->_removeIgnored($contain);

        return $contain;
    }

    /**
     * Removes any ignored associations, as defined in the model settings, from
     * the $this->contain array.
     *
     * @param array $contain data
     * @return bool
     */
    protected function _removeIgnored($contain)
    {
        $ignore = array_unique($this->getConfig('ignore'));
        if (!$ignore) {
            return $contain;
        }
        foreach ($ignore as $path) {
            $contain = Hash::remove($contain, $path);
        }

        return $contain;
    }

    /**
     * Strips primary keys and other unwanted fields
     * from hasOne and hasMany records.
     *
     * @param object $table model object
     * @param array $record
     * @return array $record
     */
    protected function _convertChildren(Table $table, Entity $record)
    {
        $assocs = $table->associations();
        $children = array_merge($assocs->getByType('HasMany'), $assocs->getByType('HasOne'));
        foreach ($children as $key => $val) {
            $property = $val->getProperty();
            if (!$record->has($property)) {
                continue;
            }
            $child = $record->{$property};

            if (is_array($child)) {
                foreach ($child as $innerKey => $innerVal) {
                    $child[$innerKey] = $this->_stripFields($innerVal);

                    $foreignKey = $val->getForeignKey();
                    if ($innerVal->has($foreignKey)) {
                        $innerVal->unsetProperty($foreignKey);
                    }

                    $child[$innerKey] = $this->_convertChildren($val->getTarget(), $child[$innerKey]);
                }
            } elseif ($child instanceof EntityInterface) {
                $child = $this->_stripFields($child);

                $foreignKey = $val->getForeignKey();
                if ($child->has($foreignKey)) {
                    $child->unsetProperty($foreignKey);
                }

                $child = $this->_convertChildren($val->getTarget(), $child);
            }
            $record->{$property} = $child;
        }

        return $record;
    }

    /**
     * Strips primary and parent foreign keys (where applicable)
     * from $this->record in preparation for saving.
     *
     * When `autoFields` is set, it will iterate listed fields and append
     * ' (copy)' for titles or '-copy' for slug/alias fields.
     *
     * Plugins can also perform custom/additional data conversion by listening
     * on `Behavior.Copyable.convertData`
     *
     * @return \Cake\ORM\Entity $this->record
     */
    protected function _convertData()
    {
        $this->record = clone $this->record;
        $this->_stripFields($this->record);

        $this->record = $this->_convertHabtm($this->_table, $this->record);
        $this->record = $this->_convertChildren($this->_table, $this->record);

        $autoFields = (array)$this->getConfig('autoFields');
        $slugFields = ['slug', 'alias'];
        foreach ($autoFields as $field) {
            if (!$this->record->has($field)) {
                continue;
            }
            if (in_array($field, $slugFields)) {
                $this->record->{$field} .= '-copy';
            } else {
                $this->record->{$field} .= ' (copy)';
            }
        }

        $eventName = 'Behavior.Copyable.convertData';
        $event = Croogo::dispatchEvent($eventName, $this->_table, [
            'record' => $this->record,
        ]);

        $this->record = $event->getData('record');

        return $this->record;
    }

    /**
     * Loops through any HABTM results in $this->record and plucks out
     * the join table info, stripping out the join table primary
     * key and the primary key of $Model. This is done instead of
     * a simple collection of IDs of the associated records, since
     * HABTM join tables may contain extra information (sorting
     * order, etc).
     *
     * @param \Cake\ORM\Table $table Table object
     * @param \Cake\ORM\Entity $record
     * @return \Cake\ORM\Entity Modified $record
     */
    protected function _convertHabtm(Table $table, $record)
    {
        if (!$this->getConfig('habtm')) {
            return $record;
        }

        $belongsToMany = $table->associations()->getByType('BelongsToMany');
        foreach ($belongsToMany as $key => $val) {
            // retrieve the reverse association
            $hasMany = $val->getTarget()->association($val->junction()->getAlias());
            $property = $hasMany->getProperty();

            if (!$record->has($property) || empty($record->{$property})) {
                continue;
            }

            foreach ($record->{$property} as $joinKey => $joinVal) {
                $joinVal = $this->_stripFields($joinVal);
                $foreignKey = $val->getForeignKey();
                if ($joinVal->has($foreignKey)) {
                    $joinVal->unsetProperty($foreignKey);
                }
            }
        }

        return $record;
    }

    /**
     * Generates a contain array for Containable behavior by
     * recursively looping through $Model->hasMany and
     * $Model->hasOne associations.
     *
     * @param object $table Model object
     * @return array
     */
    protected function _recursiveChildContain(Table $table)
    {
        $contain = [];
        if (!$this->getConfig('recursive')) {
            return $contain;
        }

        $assocs = $table->associations();
        $children = array_merge($assocs->getByType('HasMany'), $assocs->getByType('HasOne'));
        foreach ($children as $child) {
            $target = $child->getTarget();
            if ($table->getAlias() == $target->getAlias()) {
                continue;
            }
            $contain[$target->getAlias()] = $this->_recursiveChildContain($target);
        }

        return $contain;
    }

    /**
     * Strips unwanted fields from $record, taken from
     * the 'stripFields' setting.
     *
     * @param array $record
     * @return \Cake\ORM\Entity
     */
    protected function _stripFields($record)
    {
        $stripFields = (array)$this->getConfig('stripFields');
        foreach ($stripFields as $field) {
            if ($record->has($field)) {
                $record->unsetProperty($field);
                $record->isNew(true);
            }
        }

        return $record;
    }
}
