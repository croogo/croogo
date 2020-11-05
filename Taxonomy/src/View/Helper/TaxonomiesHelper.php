<?php
declare(strict_types=1);

namespace Croogo\Taxonomy\View\Helper;

use Cake\Event\Event;
use Cake\View\Helper;
use Croogo\Core\Croogo;

/**
 * Taxonomies Helper
 *
 * @category Taxonomy.View/Helper
 * @package  Croogo.Taxonomy
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 * @property \Cake\View\Helper\HtmlHelper $Html
 */
class TaxonomiesHelper extends Helper
{

    public $helpers = [
        'Html',
    ];

    public function implementedEvents(): array
    {
        return parent::implementedEvents() + [
            'Helper.Layout.beforeFilter' => 'filter',
        ];
    }

    /**
     * beforeRender
     */
    public function beforeRender($viewFile)
    {
        $request = $this->getView()->getRequest();
        if ($request->getParam('prefix') === 'Admin' && !$request->is('ajax')) {
            $this->_adminTabs();
        }
    }

    /**
     * Hook admin tabs when $taxonomy is set
     */
    protected function _adminTabs()
    {
        $request = $this->getView()->getRequest();
        $controller = $request->getParam('controller');
        $taxonomies = $this->getView()->get('taxonomies');
        if (empty($taxonomies) || $controller == 'Terms') {
            return;
        }
        $title = __d('croogo', 'Terms');
        $element = 'Croogo/Taxonomy.terms_tab';
        Croogo::hookAdminTab('Admin/' . $controller . '/add', $title, $element);
        Croogo::hookAdminTab('Admin/' . $controller . '/edit', $title, $element);
    }

    /**
     * Filter content for Vocabularies
     *
     * Replaces [vocabulary:vocabulary_alias] or [v:vocabulary_alias] with Terms list
     *
     * @param Event $event
     * @return string
     */
    public function filter(Event $event, $options = [])
    {
        $data = $event->getData();
        preg_match_all('/\[(vocabulary|v):([A-Za-z0-9_\-]*)(.*?)\]/i', $data['content'], $tagMatches);
        for ($i = 0, $ii = count($tagMatches[1]); $i < $ii; $i++) {
            $regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
            preg_match_all($regex, $tagMatches[3][$i], $attributes);
            $vocabularyAlias = $tagMatches[2][$i];
            $options = [];
            for ($j = 0, $jj = count($attributes[0]); $j < $jj; $j++) {
                $options[$attributes[1][$j]] = $attributes[2][$j];
            }
            $data['content'] = str_replace(
                $tagMatches[0][$i],
                $this->vocabulary($vocabularyAlias, $options),
                $data['content']
            );
        }

        return $event->getData();
    }

    /**
     * Show Vocabulary by Alias
     *
     * @param string $vocabularyAlias Vocabulary alias
     * @param array $options (optional)
     * @return string
     */
    public function vocabulary($vocabularyAlias, $options = [])
    {
        $_options = [
            'tag' => 'ul',
            'tagAttributes' => [],
            'type' => null,
            'link' => true,
            'plugin' => 'Croogo/Nodes',
            'controller' => 'Nodes',
            'action' => 'term',
            'element' => 'Croogo/Taxonomy.vocabulary',
        ];
        $options = array_merge($_options, $options);

        $output = '';
        $vocabulariesForLayout = $this->_View->get('vocabulariesForLayout');
        if (isset($vocabulariesForLayout[$vocabularyAlias]['threaded'])) {
            $vocabulary = $vocabulariesForLayout[$vocabularyAlias];
            $output .= $this->_View->element(
                $options['element'],
                [
                    'vocabulary' => $vocabulary,
                    'options' => $options,
                ]
            );
        }

        return $output;
    }

    /**
     * Nested Terms
     *
     * @param array $terms
     * @param array $options
     * @param int $depth
     */
    public function nestedTerms($terms, $options, $depth = 1)
    {
        $_options = [];
        $options = array_merge($_options, $options);

        $output = '';
        foreach ($terms as $term) {
            if ($options['link']) {
                $termAttr = [
                    'id' => 'term-' . $term->term->id,
                ];
                $termOutput = $this->Html->link(
                    $term->term->title,
                    [
                        'prefix' => false,
                        'plugin' => $options['plugin'],
                        'controller' => $options['controller'],
                        'action' => $options['action'],
                        'type' => $options['type'],
                        'term' => $term->term->slug,
                    ],
                    $termAttr
                );
            } else {
                $termOutput = $term->term->title;
            }
            if (isset($term['children']) && count($term['children']) > 0) {
                $termOutput .= $this->nestedTerms($term['children'], $options, $depth + 1);
            }
            $termOutput = $this->Html->tag('li', $termOutput);
            $output .= $termOutput;
        }
        if ($output != null) {
            $output = $this->Html->tag($options['tag'], $output, $options['tagAttributes']);
        }

        return $output;
    }

    /**
     * Generate string of type links
     *
     * @param array $typeData Array of Type records
     * @param array $termData Array of Term records
     * @return string
     */
    public function generateTypeLinks($typeData, $termData)
    {
        $typeLinks = '';
        $typeLink = [];
        $typeLink[] = ' (';

        foreach ((array)$typeData as $type) {
            $typeLink[] = $this->Html->link(
                $type->title,
                [
                    'prefix' => false,
                    'plugin' => 'Croogo/Nodes',
                    'controller' => 'Nodes',
                    'action' => 'term',
                    'type' => $type->alias,
                    'term' => $termData->slug,
                ],
                [
                    'target' => '_blank',
                ]
            );
        }

        $typeLink[] = ')';
        $typeLinks = implode(' ', $typeLink);

        return $typeLinks;
    }
}
