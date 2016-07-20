<?php

namespace Croogo\Taxonomy\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Croogo\Core\Core\Exception\Exception;
use Croogo\Taxonomy\Model\Entity\Type;
use Croogo\Taxonomy\Model\Entity\Vocabulary;
use Croogo\Taxonomy\Model\Table\TaxonomiesTable;

/**
 * Taxonomies Component
 *
 * @category Component
 * @package  Croogo.Taxonomy.Controller.Component
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TaxonomiesComponent extends Component
{

    /**
     * Other components used by this component
     *
     * @var array
     * @access public
     */
    public $components = [
        'Croogo/Core.Croogo',
    ];

    /**
     * Types for layout
     *
     * @var string
     * @access public
     */
    public $typesForLayout = [];

    /**
     * Vocabularies for layout
     *
     * @var string
     * @access public
     */
    public $vocabulariesForLayout = [];

    /**
     * @var \Croogo\Taxonomy\Model\Table\TaxonomiesTable
     */
    public $Taxonomies;

    /**
     * Startup
     *
     * @param object $controller instance of controller
     * @return void
     */
    public function startup(Event $event)
    {
        $this->controller = $event->subject();
        if ((isset($this->controller->Taxonomies)) && ($this->controller->Taxonomies instanceof TaxonomiesTable)) {
            $this->Taxonomies = $this->controller->Taxonomies;
        } else {
            $this->Taxonomies = TableRegistry::get('Croogo/Taxonomy.Taxonomies');
        }

        if ($this->controller->request->param('prefix') !== 'admin' &&
            !isset($this->controller->request->params['requested'])
        ) {
            $this->types();
            $this->vocabularies();
        } else {
            $this->_adminData();
        }
    }

    public function beforeRender(Event $event)
    {
        $this->controller = $event->subject();
        $this->controller->set('typesForLayout', $this->typesForLayout);
        $this->controller->set('vocabulariesForLayout', $this->vocabulariesForLayout);
    }

    /**
     * Set variables for admin layout
     *
     * @return void
     */
    protected function _adminData()
    {
        // types
        $types = $this->Taxonomies->Vocabularies->Types->find()
            ->where([
                'Types.plugin IS' => null
            ])
            ->orderAsc('Types.alias');
        $this->controller->set('typesForAdminLayout', $types);

        // vocabularies
        $vocabularies = $this->Taxonomies->Vocabularies->find()
            ->where([
                'Vocabularies.plugin IS' => null
            ])
            ->orderAsc('Vocabularies.alias');
        $this->controller->set('vocabulariesForAdminLayout', $vocabularies);
    }

    /**
     * Types
     *
     * Types will be available in this variable in views: $typesForLayout
     *
     * @return void
     */
    public function types()
    {
        $types = $this->Taxonomies->Vocabularies->Types->find('all');
        foreach ($types as $type) {
            $this->typesForLayout[$type->alias] = $type;
        }
    }

    /**
     * Vocabularies
     *
     * Vocabularies will be available in this variable in views: $vocabulariesForLayout
     *
     * @return void
     */
    public function vocabularies()
    {
        $vocabularies = [];

        if (Configure::read('Site.theme')) {
            $themeData = $this->Croogo->getThemeData(Configure::read('Site.theme'));
            if (isset($themeData['vocabularies']) && is_array($themeData['vocabularies'])) {
                $vocabularies = Hash::merge($vocabularies, $themeData['vocabularies']);
            }
        }

        $vocabularies = Hash::merge(
            $vocabularies,
            array_keys($this->controller->BlocksHook->blocksData['vocabularies'])
        );
        $vocabularies = array_unique($vocabularies);
        foreach ($vocabularies as $vocabularyAlias) {
            $vocabulary = $this->Taxonomies->Vocabularies->find()
                ->where(
                    [
                        'Vocabularies.alias' => $vocabularyAlias,
                    ]
                )
                ->applyOptions(
                    [
                        'name' => 'vocabulary_' . $vocabularyAlias,
                        'config' => 'croogo_vocabularies',
                    ]
                )
                ->first();
            if (isset($vocabulary->id)) {
                $threaded = $this->Taxonomies->find('threaded')
                    ->where(
                        [
                            'Taxonomies.vocabulary_id' => $vocabulary->id,
                        ]
                    )
                    ->order(
                        [
                            'Taxonomies.lft ASC',
                        ]
                    )
                    ->applyOptions(
                        [
                            'name' => 'vocabulary_threaded_' . $vocabularyAlias,
                            'config' => 'croogo_vocabularies',
                        ]
                    )
                    ->contain(
                        [
                            'Terms',
                        ]
                    );

                $this->vocabulariesForLayout[$vocabularyAlias] = [];
                $this->vocabulariesForLayout[$vocabularyAlias]['vocabulary'] = $vocabulary;
                $this->vocabulariesForLayout[$vocabularyAlias]['threaded'] = $threaded;
            }
        }
    }

    /**
     * Prepare required taxonomy baseline data for use in views
     *
     * @param array $type Type data
     * @param array $options Options
     * @return void
     * @throws Exception
     */
    public function prepareCommonData(Type $type, $options = [])
    {
        $options = Hash::merge(
            [
                'modelClass' => $this->controller->modelClass,
            ],
            $options
        );
        $typeAlias = $type->alias;
        list(, $modelClass) = pluginSplit($options['modelClass']);

        if (isset($this->controller->{$modelClass})) {
            $table = $this->controller->{$modelClass};
        } else {
            throw new Exception(
                sprintf(
                    'Model %s not found in controller %s',
                    $modelClass,
                    $this->controller->name
                )
            );
        }
        $table->type = $typeAlias;
        $vocabularies = collection($type->vocabularies)->combine('id', function ($vocabulary) {
            return $vocabulary;
        });
        $taxonomies = $vocabularies->map(function ($vocabulary) use ($table) {
            return $table->Taxonomies->getTree(
                $vocabulary->alias,
                ['taxonomyId' => true]
            );
        });
        $vocabularies = $vocabularies->toArray();
        $taxonomies = $taxonomies->toArray();
        $this->controller->set(
            compact(
                'type',
                'typeAlias',
                'taxonomies',
                'vocabularies'
            )
        );
    }
}
