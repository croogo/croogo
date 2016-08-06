<?php

namespace Croogo\Core\TestSuite\Constraint;
use Cake\Core\App;
use Cake\Datasource\EntityInterface;
use PHPUnit_Framework_Constraint;

/**
 * Constraint that asserts that the object it is evaluated for has a given
 * attribute.
 *
 * The attribute name is passed in the constructor.
 *
 * @since Class available since Release 3.0.0
 */
class EntityHasProperty extends PHPUnit_Framework_Constraint
{
    /**
     * @var string
     */
    protected $propertyName;

    /**
     * @param string $propertyName
     */
    public function __construct($propertyName)
    {
        parent::__construct();

        $this->propertyName = $propertyName;
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param mixed $entity Value or object to evaluate.
     *
     * @return bool
     */
    protected function matches($entity)
    {
        if (!$entity instanceof EntityInterface) {
            throw new \InvalidArgumentException();
        }

        return $entity->has($this->propertyName);
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return sprintf(
            'has property "%s"',
            $this->propertyName
        );
    }

    /**
     * Returns the description of the failure
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param mixed $other Evaluated value or object.
     *
     * @return string
     */
    protected function failureDescription($other)
    {
        return sprintf(
            'entity "%s" %s',
            App::shortName(get_class($other), 'Model/Entity'),
            $this->toString()
        );
    }
}
