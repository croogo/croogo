<?php

namespace Croogo\Core\Utility;

use Cake\Collection\CollectionInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Log\LogTrait;
use Cake\Network\Request;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\Utility\Hash;
use Cake\Routing\Router;
use Croogo\Blocks\Model\Entity\Block;
use Psr\Log\LogLevel;

/**
 * VisibilityFilter
 *
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @package  Croogo.Croogo.Lib.Utility
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class VisibilityFilter
{

    use LogTrait;

/**
 * StringConverter instance
 */
    protected $_converter = null;

/**
 * Known url keys
 */
    protected $_urlKeys = [
        'admin' => false,
        'plugin' => false,
        'controller' => false,
        'action' => false,
        'named' => false,
        'pass' => false,
    ];

/**
 * Constructor
 *
 * @param Request $request
 */
    public function __construct(Request $request = null)
    {
        if ($request) {
            $this->_request = $request;
        } else {
            $this->_request = new Request();
        }
        $this->_converter = new StringConverter();
    }

/**
 * Check that request (passed in the constructor) is visible based on list of
 * specified rules.  The rules can specified in link string format or just a
 * plain URL fragment.  Whenever possible, use link string formatted rule since
 * a URL fragment can be expensive.
 *
 * The current request is checked against negative rules first (implicitly
 * hidden), then against positive rules (implicitly visible).
 * If there's no positive rule, defaults to visible.
 *
 * @param array $rules Array of rules in link string format
 * @return bool True if the rules are satisfied
 * @see StringConverter::linkStringToArray()
 */
    protected function _isVisible($rules)
    {
        $negativeRules = array_filter($rules, function ($value) {
            if ($value[0] === '-') {
                return true;
            }
            return false;
        });
        foreach ($negativeRules as $rule) {
            if ($this->_ruleMatch(substr($rule, 1))) {
                return false;
            }
        }

        $positiveRules = array_diff($rules, $negativeRules);
        if (empty($positiveRules)) {
            return true;
        }
        foreach ($positiveRules as $rule) {
            if ($rule[0] == '+') {
                $rule = substr($rule, 1);
            }
            if ($this->_ruleMatch($rule)) {
                return true;
            }
        }

        return false;
    }

/**
 * Check that request matches a single rule
 *
 * @param string $rule Rule in link string or plain URL fragment
 * @return bool True if request satisfies the rule
 */
    protected function _ruleMatch($rule)
    {
        if (strpos($rule, ':') !== false) {
            $url = array_filter($this->_converter->linkStringToArray($rule));
            if (isset($url['?'])) {
                $queryString = $url['?'];
                unset($url['?']);
            }
        } else {
            $url = Router::parse($rule);
            $named = array_diff_key($url, $this->_urlKeys);
            $url['named'] = $named;
        }

        $intersect = array_intersect_key($this->_request->params, $url);
        $matched = $intersect == $url;

        if ($matched && isset($queryString)) {
            $matched = $this->_request->query == $queryString;
        }

        return $matched;
    }

    /**
     * Remove values based on rules in visibility_path field.
     *
     * Options:
     *   - field Field name containing the visibility path rules
     *
     * @param \Traversable $traversable
     * @param array $options
     * @return \Cake\Collection\Collection
     */
    public function remove(\Traversable $traversable, $options = [])
    {
        $options = Hash::merge([
            'field' => null,
        ], $options);
        $field = $options['field'];

        return collection($traversable)->filter(function (Entity $entity) use ($field) {
            $rules = $entity->get($field);
            if (empty($rules)) {
                return true;
            }

            if (!is_array($rules)) {
                $this->log('Invalid visibility_path rule', LogLevel::ERROR);
            }

            return $this->_isVisible($rules);
        });
    }
}
