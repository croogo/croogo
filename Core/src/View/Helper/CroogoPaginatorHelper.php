<?php

namespace Croogo\Core\View\Helper;

use Cake\View\Helper\PaginatorHelper;

/**
 * Croogo Paginator Helper
 *
 * @package Croogo.Croogo.View.Helper
 */
class CroogoPaginatorHelper extends PaginatorHelper
{

    public $helpers = [
        'Html',
        'Url'
    ];

/**
 * doesn't use parent::numbers()
 *
 * @param array $options
 * @return boolean
 */
    public function numbers(array $options = [])
    {
        $defaults = [
            'tag' => 'li',
            'model' => $this->defaultModel(),
            'modulus' => '8',
            'class' => null
        ];
        $options = array_merge($defaults, $options);
        extract($options);

        $params = $this->params($options['model']);
        extract($params);

        $begin = $page - floor($modulus / 2);
        $end = $begin + $modulus;
        if ($end > $pageCount) {
            $end = $pageCount + 1;
            $begin = $pageCount - $modulus;
        }
        $begin = $begin <= 0 ? 1 : $begin;

        $output = '';
        for ($i = $begin; $i < $end; $i++) {
            $class = ($i == $page) ? 'active' : '';
            $output .= $this->Html->tag($tag, $this->Html->link($i, ['page' => $i], compact('class')));
        }
        return $output;
    }

    protected function _defaultOptions($options)
    {
        if (!isset($options['tag'])) {
            $options['tag'] = 'li';
        }

        return $options;
    }

    public function prev($title = '<< Previous', array $options = [])
    {
        $options['escape'] = isset($options['escape']) ? $options['escape'] : false;
        $options = $this->_defaultOptions($options, false);
        return parent::prev($title = '<< Previous', $options = []);
    }

    public function next($title = '<< Previous', array $options = [])
    {
        $options['escape'] = isset($options['escape']) ? $options['escape'] : false;
        $options = $this->_defaultOptions($options, false);
        return parent::next($title = 'Next >>', $options = []);
    }

    public function first($first = '<< first', array $options = [])
    {
        $options['escape'] = isset($options['escape']) ? $options['escape'] : true;
        $options = $this->_defaultOptions($options);
        return parent::first($first, $options);
    }

    public function last($last = 'last >>', array $options = [])
    {
        $options['escape'] = isset($options['escape']) ? $options['escape'] : true;
        $options = $this->_defaultOptions($options);
        return parent::last($last, $options);
    }
}
