<?php

namespace Croogo\Core\View\Widget;

use Cake\Utility\Hash;
use Cake\View\Form\ContextInterface;
use Cake\View\Widget\WidgetInterface;

class StringListWidget implements WidgetInterface
{

    protected $_templates;

    public function __construct($templates)
    {
        $this->_templates = $templates;
    }

    public function render(array $data, ContextInterface $context)
    {
        $values = [];
        foreach ((array)$data['val'] as $k => $v) {
            if (is_numeric($k)) {
                $values[] = $v;
            } else {
                $values[] = $k . '=' . $v;
            }
        }
        $data = Hash::merge(['class' => 'form-control'], $data);
        return $this->_templates->format('textarea', [
            'class' => 'textarea stringlist',
            'name' => $data['name'],
            'value' => implode("\n", $values),
            'attrs' => $this->_templates->formatAttributes($data, [
                'type', 'name', 'val', 'before', 'after',
            ]),
        ]);
    }

    public function secureFields(array $data)
    {
        return [$data['name']];
    }
}
