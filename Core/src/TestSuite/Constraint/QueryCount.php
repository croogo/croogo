<?php

namespace Croogo\Core\TestSuite\Constraint;
use Cake\Core\App;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use PHPUnit_Framework_Constraint;

/**
 * Constraint that asserts that the object it is evaluated for has a given
 * attribute.
 *
 * The attribute name is passed in the constructor.
 *
 * @since Class available since Release 3.0.0
 */
class QueryCount extends PHPUnit_Framework_Constraint
{
    /**
     * @var string
     */
    protected $count;

    /**
     * @param string $count
     */
    public function __construct($count)
    {
        parent::__construct();

        $this->count = $count;
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param mixed $query Value or object to evaluate.
     *
     * @return bool
     */
    protected function matches($query)
    {
        if (!$query instanceof Query) {
            throw new \InvalidArgumentException();
        }

        return $query->count() === $this->count;
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return sprintf(
            'count gives %d',
            $this->count
        );
    }

    /**
     * Returns the description of the failure
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param mixed $query Evaluated value or object.
     *
     * @return string
     */
    protected function failureDescription($query)
    {
        if (!$query instanceof Query) {
            return;
        }

        return sprintf(
            'query from repository "%s" %s. %d given',
            $query->repository()->alias(),
            $this->toString(),
            $query->count()
        );
    }
}
