<?php
declare(strict_types=1);

namespace Croogo\Core\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Query;

class SluggableBehavior extends Behavior
{

    protected $_defaultConfig = [
        'slugField' => 'slug',
    ];

    public function findBySlug(Query $query, array $options = [])
    {
        if (empty($options['search']['slug'])) {
            return $query;
        }

        $slugField = $this->_config['slugField'];
        if ($this->_table->behaviors()->has('Translate')) {
            $query->where([
                $this->_table->translationField($slugField) . ' LIKE'=> '%' . $options['search']['slug'] . '%',
            ]);
        } else {
            $query->where([
                $this->_table->aliasField($slugField) . ' LIKE' => '%' . $options['search']['slug'] . '%',
            ]);
        }
        return $query;
    }

}