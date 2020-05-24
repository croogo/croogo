<?php

namespace Croogo\Meta\View\Helper;

use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\Utility\Text;
use Cake\View\Helper;
use Croogo\Core\Utility\StringConverter;

/**
 * Meta Helper
 *
 * @category Meta.View/Helper
 * @package  Croogo.Meta
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 *
 * @property \Croogo\Core\View\Helper\HtmlHelper $Html
 */
class MetaHelper extends Helper
{

    /**
     * Helpers
     */
    public $helpers = [
        'Url',
        'Croogo/Core.Layout',
        'Croogo/Core.Croogo',
        'Html' => [
            'className' => 'Croogo/Core.Html'
        ],
        'Form' => [
            'className' => 'Croogo/Core.Form'
        ],
    ];

    public $settings = [
        'deleteUrl' => [
            'prefix' => 'admin', 'plugin' => 'Croogo/Meta',
            'controller' => 'Meta', 'action' => 'deleteMeta',
        ],
    ];

    /**
     * beforeRender
     */
    public function beforeRender($viewFile)
    {
        if ($this->Layout->isLoggedIn()) {
            return $this->Croogo->adminScript('Croogo/Meta.admin');
        }
    }

    /**
     * Meta tags
     *
     * @return string
     */
    public function meta($metaForLayout = [])
    {
        $_metaForLayout = Configure::read('Meta.data');
        $node = $this->getView()->get('node');
        $node = $node ?: $this->getView()->get('entity');
        $nodeMeta = isset($node['meta'])
            ? $node['meta']
            : [];
        if (count($nodeMeta) > 0) {
            $metaForLayout = [];
            foreach ($nodeMeta as $index => $meta) {
                $value = $meta->value;
                if (strstr($value, 'youtu.be') !== false) {
                    $parsed = parse_url($value);
                    $value = sprintf('https://youtube.com/embed%s', $parsed['path']);
                }
                $metaForLayout[$meta->key] = $value;
            }
        }

        // fallback for meta_description from node
        $key = 'meta_description';
        if ($node && !array_key_exists($key, $metaForLayout)) {
            $converter = new StringConverter();
            if (!empty($node['excerpt'])) {
                $metaForLayout[$key] = strip_tags($node['excerpt']);
            } else {
                $metaForLayout[$key] = $converter->firstPara($node['body']);
            }
        }

        // fallback for rel_canonical from node
        $key = 'rel_canonical';
        if ($node && !array_key_exists($key, $metaForLayout)) {
            if (!empty($node['path'])) {
                $metaForLayout[$key] = $node['path'];
            }
        }

        $metaForLayout = array_merge($_metaForLayout, $metaForLayout);

        $output = '';
        foreach ($metaForLayout as $key => $value) {
            if (strstr($key, 'meta_')) {
                $output .= $this->Html->meta([
                    'name' => str_replace('meta_', '', $key),
                    'content' => $value,
                ]);
            } elseif (strstr($key, 'rel_')) {
                $template = '<link rel="canonical" href="%s"/>';
                $output .= sprintf($template, $this->Url->build($value, [
                    'fullBase' => true,
                ]));
            } elseif (strstr($key, 'og:')) {
                $output .= $this->Html->meta([
                    'property' => $key,
                    'content' => $value,
                ]);
            } else {
                $output .= $this->Html->meta([
                    'name' => $key,
                    'content' => $value,
                ]);
            }
        }

        return $output;
    }

    /**
     * Meta field: with key/value fields
     *
     * @param string $key (optional) key
     * @param string $value (optional) value
     * @param int $id (optional) ID of Meta
     * @param array $options (optional) options
     * @return string
     */
    public function field($key = '', $value = null, $id = null, $options = [])
    {
        $_options = [
            'uuid' => Text::uuid(),
            'key' => [
                'label' => __d('croogo', 'Key'),
                'value' => $key,
            ],
            'value' => [
                'label' => __d('croogo', 'Value'),
                'value' => $value,
                'type' => 'textarea',
                'rows' => 2,
            ],
        ];
        $options = Hash::merge($_options, $options);
        $uuid = $options['uuid'];
        $isTab = isset($options['tab']);

        if ($isTab) {
            if (empty($options['key']['type'])) {
                $options['key']['type'] = 'hidden';
            }
            if (empty($options['value']['label'])) {
                $options['value']['label'] = Inflector::humanize($key);
            }
        }
        $fields = '';
        if ($id != null) {
            $fields .= $this->Form->input('meta.' . $uuid . '.id', [
                'type' => 'hidden',
                'value' => $id,
                'class' => 'meta-id',
            ]);
            $this->Form->unlockField('meta.' . $uuid . '.id');
        }
        $options['value']['data-metafield'] = $key;
        $fields .= $this->Form->input('meta.' . $uuid . '.key', $options['key']);
        $fields .= $this->Form->input('meta.' . $uuid . '.value', $options['value']);
        $this->Form->unlockField('meta.' . $uuid . '.key');
        $this->Form->unlockField('meta.' . $uuid . '.value');
        $fields = $this->Html->tag('div', $fields, ['class' => 'fields']);

        $id = is_null($id) ? $uuid : $id;
        $actions = null;
        if (!$isTab) {
            $deleteUrl = $this->settings['deleteUrl'];
            $deleteUrl[] = $id;
            $actions = $this->Html->link(
                __d('croogo', 'Remove'),
                $deleteUrl,
                ['class' => 'btn btn-outline-danger remove-meta', 'rel' => 'meta-field-' . $id]
            );
            $actions = $this->Html->tag('div', $actions, ['class' => 'actions my-3']);
        }

        $output = $this->Html->tag('div', $fields . $actions, [
            'class' => 'meta-field',
            'id' => 'meta-field-' . $id,
        ]);

        return $output;
    }
}
