<?php

namespace Croogo\Core\View\Helper;

use Cake\View\Helper\PaginatorHelper;
use Cake\View\View;

/**
 * Croogo Paginator Helper
 *
 * @package Croogo.Croogo.View.Helper
 */
class CroogoPaginatorHelper extends PaginatorHelper
{

    /**
     * Constructor. Overridden to merge passed args with URL options.
     *
     * @param \Cake\View\View $View The View this helper is being attached to.
     * @param array $config Configuration settings for the helper.
     */
    public function __construct(View $View, array $config = [])
    {
        $this->_defaultConfig['templates'] = [
                'nextActive' => '<li class="next page-item"><a rel="next" aria-label="Next" href="{{url}}" class="page-link">' .
                    '<span aria-hidden="true">{{text}}</span></a></li>',
                'nextDisabled' => '<li class="next page-item disabled"><a class="page-link"><span aria-hidden="true">{{text}}</span></a></li>',
                'prevActive' => '<li class="prev page-item"><a rel="prev" aria-label="Previous" href="{{url}}" class="page-link">' .
                    '<span aria-hidden="true">{{text}}</span></a></li>',
                'prevDisabled' => '<li class="prev page-item disabled"><a class="page-link"><span aria-hidden="true">{{text}}</span></a></li>',
                'current' => '<li class="page-item active"><a class="page-link">{{text}} <span class="sr-only">(current)</span></a></li>',
                'number' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
                'first' => '<li class="first page-item"><a href="{{url}}" class="page-link">{{text}}</a></li>',
                'last' => '<li class="last page-item"><a href="{{url}}" class="page-link">{{text}}</a></li>',
            ] + $this->_defaultConfig['templates'];

        parent::__construct($View, $config);
    }

/**
 * @param array $options
 * @return boolean
 */
    public function numbers(array $options = [])
    {
        return parent::numbers($options);
    }
}
