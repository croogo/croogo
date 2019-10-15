<?php

namespace Croogo\Blocks\Controller\Component;

use Cake\Cache\Cache;
use Cake\Collection\Collection;
use Cake\Controller\Component;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use Croogo\Blocks\Model\Entity\Block;
use Croogo\Core\Utility\StringConverter;
use Croogo\Core\Utility\VisibilityFilter;

/**
 * Blocks Component
 *
 * @package Croogo.Blocks.Controller.Component
 */
class BlocksComponent extends Component
{

    /**
     * Blocks for layout
     *
     * @var string
     * @access public
     */
    public $blocksForLayout = [];

    /**
     * Blocks data: contains parsed value of bb-code like strings
     *
     * @var array
     * @access public
     */
    public $blocksData = [
        'menus' => [],
        'vocabularies' => [],
        'nodes' => [],
    ];

    /**
     * @var StringConverter
     */
    protected $_stringConverter = null;

    /**
     * initialize
     *
     * @param Event $event
     */
    public function beforeFilter(Event $event)
    {
        $this->controller = $event->getSubject();
        $this->_stringConverter = new StringConverter();
        if (isset($this->controller->Blocks)) {
            $this->Blocks = $this->controller->Blocks;
        } else {
            $this->Blocks = TableRegistry::get('Croogo/Blocks.Blocks');
        }
    }

    /**
     * Startup
     *
     * @param Event $event
     * @return void
     */
    public function startup(Event $event)
    {
        if ($this->request->getParam('prefix') !== 'admin' && !$this->request->getParam('requested')) {
            $this->blocks();
        }
    }

    /**
     * beforeRender
     *
     * @param object $event
     * @return void
     */
    public function beforeRender(Event $event)
    {
        $event->getSubject()->set('blocksForLayout', $this->blocksForLayout);
    }

    /**
     * Blocks
     *
     * Blocks will be available in this variable in views: $blocksForLayout
     *
     * @return void
     */
    public function blocks()
    {
        $this->blocksForLayout = [];
        $regions = $this->Blocks->Regions->find('active')->find('list', [
            'valueField' => 'alias'
        ]);

        $alias = $this->Blocks->getAlias();
        $roleId = $this->controller->Croogo->roleId();
        $status = $this->Blocks->status();
        $request = $this->controller->request;
        $slug = Text::slug(strtolower($request->getPath()));
        $Filter = new VisibilityFilter($request);
        foreach ($regions as $regionId => $regionAlias) {
            $cacheKey = $regionAlias . '_' . $roleId;
            $this->blocksForLayout[$regionAlias] = [];

            $visibilityCachePrefix = 'visibility_' . $slug . '_' . $cacheKey;
            $blocks = Cache::read($visibilityCachePrefix, 'croogo_blocks');
            if ($blocks === false) {
                $blocks = $this->Blocks->find('regionPublished', [
                    'regionId' => $regionId,
                    'roleId' => $roleId,
                    'cacheKey' => $cacheKey,
                ]);

                $blocks = $Filter->remove($blocks->all(), [
                    'field' => 'visibility_paths',
                    'cache' => [
                        'prefix' => $visibilityCachePrefix,
                        'config' => 'croogo_blocks',
                    ],
                ]);

                Cache::write($visibilityCachePrefix, $blocks->toArray(), 'croogo_blocks');
            }
            /** @var Collection $blocks */
            $blocks = collection($blocks);
            $this->processBlocksData($blocks);
            $this->blocksForLayout[$regionAlias] = $blocks->toArray();
        }
    }

    /**
     * Process blocks for bb-code like strings
     *
     * @param Collection $blocks
     * @return void
     */
    public function processBlocksData(Collection $blocks)
    {
        $converter = $this->_stringConverter;
        /** @var Block $block */
        foreach ($blocks as $block) {
            $this->blocksData['menus'] = Hash::merge(
                $this->blocksData['menus'],
                $converter->parseString('menu|m', $block->body)
            );
            $this->blocksData['vocabularies'] = Hash::merge(
                $this->blocksData['vocabularies'],
                $converter->parseString('vocabulary|v', $block->body)
            );
            $this->blocksData['nodes'] = Hash::merge(
                $this->blocksData['nodes'],
                $converter->parseString(
                    'node|n',
                    $block->body,
                    ['convertOptionsToArray' => true]
                )
            );
        }
    }
}
