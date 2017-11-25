<?php

namespace Croogo\Core\Database\Type;

use Cake\Database\Driver;
use Cake\Database\Type;
use Croogo\Core\Utility\StringConverter;
use PDO;

/**
 * ParamsType
 *
 * @package  Croogo.Core.Database.Type
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ParamsType extends Type
{

/**
 * @param $value
 * @param Driver $driver
 * @return array
 */
    public function toPHP($value, Driver $driver)
    {
        if (empty($value) || $value === null) {
            return [];
        }
        return $this->paramsToArray($value);
    }

/**
 * @param $value
 * @return array
 */
    public function marshal($value)
    {
        if (is_array($value) || $value === null) {
            return $value;
        }
        return $this->paramsToArray($value);
    }

/**
 * @param $value
 * @param Driver $driver
 * @return array
 */
    public function toDatabase($value, Driver $driver)
    {
        return $this->arrayToParams($value);
    }

/**
 * @param $value
 * @param Driver $driver
 * @return int
 */
    public function toStatement($value, Driver $driver)
    {
        if ($value === null) {
            return PDO::PARAM_NULL;
        }
        return PDO::PARAM_STR;
    }

/**
 * Converts a string of params to an array of formatted key/value pairs
 *
 * String is supposed to have one parameter per line in the format:
 * my_param_key=value_here
 * another_param=another_value
 *
 * @param string $params
 * @return array
 */
    public function paramsToArray($params)
    {
        $converter = new StringConverter();
        $output = [];
        $params = preg_split('/[\r\n]+/', $params);
        foreach ($params as $param) {
            if (strlen($param) == 0) {
                continue;
            }

            if ($param[0] === '[') {
                $options = $converter->parseString('options', $param, [
                    'convertOptionsToArray' => true,
                ]);
                if (!empty($options)) {
                    $output = array_merge($output, $options);
                }
                continue;
            }

            $paramE = explode('=', $param);
            if (count($paramE) == 2) {
                $key = $paramE['0'];
                $value = $paramE['1'];
                $output[$key] = trim($value);
            }
        }
        return $output;
    }

/**
 * Converts a array of formatted key/value pairs to an string of params
 *
 * @param $array
 * @return array
 */
    public function arrayToParams($array)
    {
        $params = '';
        $i = 0;
        foreach ((array)$array as $key => $value) {
            $params .= $key . '=' . $value;

            if ($i != (count($array) - 1)) {
                $params .= "\r\n";
            }

            $i++;
        }
        return $params;
    }
}
