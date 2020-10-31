<?php
declare(strict_types=1);

namespace Croogo\Translate\Model\Entity;

use Cake\ORM\Entity;

/**
 * I18n Entity
 *
 * @property int $id
 * @property string $locale
 * @property string $model
 * @property int $foreign_key
 * @property string $field
 * @property string|null $content
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int $created_by
 * @property int|null $modified_by
 */
class I18n extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'locale' => true,
        'model' => true,
        'foreign_key' => true,
        'field' => true,
        'content' => true,
        'created' => true,
        'modified' => true,
        'created_by' => true,
        'modified_by' => true,
    ];
}
