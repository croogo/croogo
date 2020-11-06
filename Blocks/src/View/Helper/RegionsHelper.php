<?php
declare(strict_types=1);

namespace Croogo\Blocks\View\Helper;

use Cake\Event\Event;
use Cake\Log\LogTrait;
use Cake\Utility\Hash;
use Cake\View\Helper;
use Croogo\Blocks\Model\Entity\Block;
use Croogo\Core\Croogo;

/**
 * Regions Helper
 *
 * @category Helper
 * @package  Croogo.Blocks.View.Helper
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class RegionsHelper extends Helper
{

    use LogTrait;

    /**
     * Region is empty
     *
     * returns true if Region has no Blocks.
     *
     * @param string $regionAlias Region alias
     * @return bool
     */
    public function isEmpty($regionAlias)
    {
        $blocksForLayout = $this->_View->get('blocksForLayout');
        if (!empty($blocksForLayout[$regionAlias])) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Show Block
     *
     * By default block is rendered using Blocks.block element. If `Block.element` is
     * set and exists, the render process will pass it through given element before wrapping
     * it inside the Blocks.block container. You disable the wrapping by setting
     * `enclosure=false` in the `params` field.
     *
     * @param Block $block Block
     * @param string $regionAlias Alias of the region
     * @param array $options
     * @return string
     */
    public function block(Block $block, $regionAlias = null, $options = [])
    {
        $output = '';

        $options = Hash::merge([
            'elementOptions' => [],
        ], $options);
        $elementOptions = $options['elementOptions'];

        $defaultElement = 'Croogo/Blocks.block';

        $element = $block->element;
        $exists = $this->_View->elementExists($element);

        $event = Croogo::dispatchEvent('Helper.Regions.beforeSetBlock', $this->_View, [
            'content' => $block->body,
        ]);
        $block->body = $event->getData()['content'];

        if ($exists) {
            $blockOutput = $this->_View->element($element, compact('block'), $elementOptions);
        } else {
            if (!empty($element)) {
                $this->log(sprintf(
                    'Missing element `%s` in block `%s` (%s)',
                    $block->element,
                    $block->alias,
                    $block->id
                ), LOG_WARNING);
            }
            $blockOutput = $this->_View->element(
                $defaultElement,
                compact('block'),
                ['ignoreMissing' => true] + $elementOptions
            );
        }

        if ($block->get('cell')) {
            $parts = explode('::', $block->get('cell'));

            if (count($parts) === 2) {
                list($pluginAndCell, $action) = [$parts[0], $parts[1]];
            } else {
                list($pluginAndCell, $action) = [$parts[0], $regionAlias];
            }

            $blockOutput = (string)$this->_View->cell($pluginAndCell . '::' . $action, [], $block->params);
        }

        Croogo::dispatchEvent('Helper.Regions.afterSetBlock', $this->_View, [
            'content' => &$blockOutput,
        ]);

        $enclosure = isset($block['Params']['enclosure']) ? $block['Params']['enclosure'] === "true" : true;
        if ($exists && $element != $defaultElement && $enclosure) {
            $block->body = $blockOutput;
            $block->element = null;
            $output .= $this->_View->element($defaultElement, compact('block'), $elementOptions);
        } else {
            $output .= $blockOutput;
        }

        return $output;
    }

    /**
     * Show Blocks for a particular Region
     *
     * By default block are rendered using Blocks.block element. If `Block.element` is
     * set and exists, the render process will pass it through given element before wrapping
     * it inside the Blocks.block container. You disable the wrapping by setting
     * `enclosure=false` in the `params` field.
     *
     * @param string $regionAlias Region alias
     * @param array $options
     * @return string
     */
    public function blocks($regionAlias, $options = [])
    {
        $output = '';
        if ($this->isEmpty($regionAlias)) {
            return $output;
        }

        $options = Hash::merge([
            'elementOptions' => [],
        ], $options);

        $blocksForLayout = $this->_View->get('blocksForLayout');
        $blocks = $blocksForLayout[$regionAlias];
        foreach ($blocks as $block) {
            $output .= $this->block($block, $regionAlias, $options);
        }

        return $output;
    }

    /**
     * @return array
     */
    public function implementedEvents(): array
    {
        $events = parent::implementedEvents();
        $events['Helper.Layout.beforeFilter'] = [
            'callable' => 'filter',
            'passParams' => true,
        ];

        return $events;
    }

    /**
     * Filter content for Scripts and css tags
     *
     * Replaces [region:alias]
     *
     * @param Event $event
     * @return string
     */
    public function filter(Event $event)
    {
        preg_match_all('/\[(region):([A-Za-z0-9_\-]*)(.*?)\]/i', $event->getData('content'), $tagMatches);
        for ($i = 0, $ii = count($tagMatches[1]); $i < $ii; $i++) {
            $regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
            preg_match_all($regex, $tagMatches[3][$i], $attributes);
            $regionAlias = $tagMatches[2][$i];
            $options = [];
            for ($j = 0, $jj = count($attributes[0]); $j < $jj; $j++) {
                $options[$attributes[1][$j]] = $attributes[2][$j];
            }
            $options = Hash::expand($options);
            $event->data['content'] = str_replace($tagMatches[0][$i], $this->blocks($regionAlias, $options), $event->data['content']);
        }

        return $event->getData();
    }
}
