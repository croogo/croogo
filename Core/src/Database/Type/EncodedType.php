<?php

namespace Croogo\Core\Database\Type;

use Cake\Database\Driver;
use Cake\Database\Type;
use Croogo\Core\Utility\StringConverter;
use PDO;

class EncodedType extends Type
{

    public function toPHP($value, Driver $driver)
    {
        if (empty($value) || $value === null) {
            return $value;
        }

        return $this->decodeData($value);
    }

    public function marshal($value)
    {
        if (is_string($value)) {
            $value = explode("\n", $value);
        }
        if (is_array($value) || $value === null) {
            return $value;
        }

        return $this->decodeData($value);
    }

    public function toDatabase($value, Driver $driver)
    {
        return $this->encodeData($value);
    }

    public function toStatement($value, Driver $driver)
    {
        if ($value === null) {
            return PDO::PARAM_NULL;
        }
        return PDO::PARAM_STR;
    }


    /**
     * Encode data
     *
     * Turn array into a JSON
     *
     * @param array $data data
     * @param array $options (optional)
     * @return string
     */
    public function encodeData($data, $options = [])
    {
        $_options = [
            'json' => false,
            'trim' => true,
        ];
        $options = array_merge($_options, $options);

        if (is_array($data) && count($data) > 0) {
            // trim
            if ($options['trim']) {
                $elements = [];
                foreach ($data as $id => $d) {
                    $d = trim($d);
                    if ($d != '') {
                        $elements[$id] = '"' . $d . '"';
                    }
                }
            } else {
                $elements = $data;
            }

            // encode
            if (count($elements) > 0) {
                if ($options['json']) {
                    $output = json_encode($elements);
                } else {
                    $output = '[' . implode(',', $elements) . ']';
                }
            } else {
                $output = '';
            }
        } else {
            $output = '';
        }

        return $output;
    }

    /**
     * Decode data
     *
     * @param string $data
     * @return array
     */
    public function decodeData($data)
    {
        if ($data == '') {
            $output = '';
        } else {
            $output = json_decode($data, true);
        }

        return $output;
    }
}
