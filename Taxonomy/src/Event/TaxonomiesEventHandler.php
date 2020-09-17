<?php

namespace Croogo\Taxonomy\Event;

use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;
use Croogo\Core\Croogo;
use Croogo\Core\Nav;

/**
 * Taxonomy Event Handler
 *
 * @category Event
 * @package  Croogo.Taxonomy.Event
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TaxonomiesEventHandler implements EventListenerInterface
{

    /**
     * implementedEvents
     */
    public function implementedEvents(): array
    {
        return [
            'Croogo.setupAdminData' => [
                'callable' => 'onSetupAdminData',
            ],
            'Controller.Links.setupLinkChooser' => [
                'callable' => 'onSetupLinkChooser',
            ],
        ];
    }

    /**
     * Setup admin data
     */
    public function onSetupAdminData($event)
    {
        $controller = $event->getSubject();

        $vocabularies = $controller->viewBuilder()->getVar('vocabulariesForAdminLayout');
        foreach ($vocabularies as $k => $v) {
            $weight = 9999 + $v->weight;
            Nav::add('sidebar', 'content.children.taxonomy.children.' . $v->alias, [
                'title' => $v->title,
                'url' => [
                    'prefix' => 'admin',
                    'plugin' => 'Croogo/Taxonomy',
                    'controller' => 'Taxonomies',
                    'action' => 'index',
                    '?' => [
                        'vocabulary_id' => $v->id,
                    ],
                ],
                'weight' => $weight,
            ]);
        };
    }

    /**
     * Setup Link chooser values
     *
     * @return void
     */
    public function onSetupLinkChooser($event)
    {
        $vocabulariesTable = TableRegistry::get('Croogo/Taxonomy.Vocabularies');
        $vocabularies = $vocabulariesTable->find('all')->contain([
            'Types'
        ]);

        $linkChoosers = [];
        foreach ($vocabularies as $vocabulary) {
            foreach ($vocabulary->types as $type) {
                $title = h($type->title . ' ' . $vocabulary->title);
                $linkChoosers[$title] = [
                    'description' => h($vocabulary->description),
                    'url' => [
                        'prefix' => 'admin',
                        'plugin' => 'Croogo/Taxonomy',
                        'controller' => 'Taxonomies',
                        'action' => 'index',
                        $vocabulary->id,
                        '?' => [
                            'type' => $type->alias,
                            'chooser' => 1,
                        ],
                    ],
                ];
            }
        }
        Croogo::mergeConfig('Croogo.linkChoosers', $linkChoosers);
    }
}
