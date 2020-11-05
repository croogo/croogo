<?php
declare(strict_types=1);

namespace Croogo\Core\Schema\JsonApi;

use Cake\Datasource\EntityInterface;
use Cake\Utility\Inflector;
use CrudJsonApi\Schema\JsonApi\DynamicEntitySchema as CrudJsonApiDynamicEntitySchema;

class DynamicEntitySchema extends CrudJsonApiDynamicEntitySchema
{

    /**
     * @inheritdoc
     */
    protected function entityToShallowArray(EntityInterface $entity)
    {
        $result = [];
        $properties = method_exists($entity, 'getVisible')
            ? $entity->getVisible()
            : $entity->visibleProperties();
        foreach ($properties as $property) {
            if ($property === '_joinData' || $property === '_matchingData') {
                continue;
            }

            $value = $entity->get($property);
            if (is_array($value)) {
                $result[$property] = [];
                foreach ($value as $k => $innerValue) {
                    if (!$innerValue instanceof EntityInterface) {
                        $result[$property][$k] = $innerValue;
                    }
                }
            } else {
                $result[$property] = $value;
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     *
     * Override to skip inflection for $input starting with `_` character
     */
    protected function inflect(object $configClass, string $input): string
    {
        if ($input[0] === '_') {
            return $input;
        }

        return parent::inflect($configClass, $input);
    }

}