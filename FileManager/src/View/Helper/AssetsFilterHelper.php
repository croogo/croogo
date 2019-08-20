<?php

namespace Croogo\FileManager\View\Helper;

use Cake\Event\Event;
use Cake\Log\LogTrait;
use Cake\ORM\TableRegistry;
use Cake\View\View;
use Cake\View\Helper;

class AssetsFilterHelper extends Helper {

    use LogTrait;

    public $helpers = array(
        'Html',
        'Croogo/Nodes.Nodes',
    );

    public function __construct(View $view, $settings = array()) {
        parent::__construct($view);
        $this->_setupEvents();
    }

    protected function _setupEvents() {
        $events = array(
            'Helper.Layout.beforeFilter' => array(
                'callable' => 'filter', 'passParams' => true,
            ),
        );
        $eventManager = $this->_View->getEventManager();
        foreach ($events as $name => $config) {
            $eventManager->on($name, $config, [$this, 'filter']);
        }
    }

    //public function filter(&$content, $options = array()) {
    public function filter(Event $event) {
        $content =& $event->result['content'];
        $options =& $event->result['options'];
        $conditions = array();
        $identifier = '';
        if (isset($options['model']) && isset($options['id'])) {
            $conditions = array(
                'AssetUsages.model' => $options['model'],
                'AssetUsages.foreign_key' => $options['id'],
            );
            $identifier = $options['model'] . '.' . $options['id'];
        }

        preg_match_all('/\[(image):[ ]*([A-Za-z0-9_\-]*)(.*?)\]/i', $content, $tagMatches);
        $AssetUsages = TableRegistry::get('Croogo/FileManager.AssetUsages');

        for ($i = 0, $ii = count($tagMatches[1]); $i < $ii; $i++) {
            $assets = $this->parseString('image|i', $tagMatches[0][$i]);
            $assetId = $tagMatches[2][$i];
            $conditions['AssetUsages.id'] = $assetId;
            $assetUsage = $AssetUsages->find()
                ->contain('Assets')
                ->where($conditions)
                ->cache('asset_filtered_' . $assetId, 'nodes')
                ->first();
            if (!$assetUsage) {
                $this->log(sprintf('%s - Asset not found for %s',
                    $identifier, $tagMatches[0][$i]
                ));
                $regex = '/' . preg_quote($tagMatches[0][$i]) . '/';
                $content = preg_replace($regex, '', $content);
                continue;
            }

            $options = !empty($assets[$assetId]) ? $assets[$assetId] : ['class' => $this->_View->Theme->getCssClass('thumbnailClass')];
            $img = $this->Html->image($assetUsage->asset->path, $options);
            $regex = '/' . preg_quote($tagMatches[0][$i]) . '/';
            $content = preg_replace($regex, $img, $content);
        }

        return $content;
    }

    public function afterSetNode() {
        $body = $this->Nodes->field('body');
        //$body = $this->filter($body, array(
        //    'model' => 'Node', 'id' => $this->Nodes->field('id')
        //));
        $body = $this->filter(new Event('Helper.Layout.beforeFilter', $this, [
            'content' => $body,
            'model' => 'Node',
            'id' => $this->Nodes->field('id'),
        ]));

        $this->Nodes->field('body', $body);
    }

/**
 * Parses bb-code like string.
 *
 * Example: string containing [menu:main option1="value"] will return an array like
 *
 * Array
 * (
 *     [main] => Array
 *         (
 *             [option1] => value
 *         )
 * )
 *
 * @param string $exp
 * @param string $text
 * @param array  $options
 * @return array
 */
    public function parseString($exp, $text, $options = array()) {
        $_options = array(
            'convertOptionsToArray' => false,
        );
        $options = array_merge($_options, $options);

        $output = array();
        preg_match_all('/\[(' . $exp . '):([A-Za-z0-9_\-]*)(.*?)\]/i', $text, $tagMatches);
        for ($i = 0, $ii = count($tagMatches[1]); $i < $ii; $i++) {
            $regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
            preg_match_all($regex, $tagMatches[3][$i], $attributes);
            $alias = $tagMatches[2][$i];
            $aliasOptions = array();
            for ($j = 0, $jj = count($attributes[0]); $j < $jj; $j++) {
                $aliasOptions[$attributes[1][$j]] = $attributes[2][$j];
            }
            if ($options['convertOptionsToArray']) {
                foreach ($aliasOptions as $optionKey => $optionValue) {
                    if (!is_array($optionValue) && strpos($optionValue, ':') !== false) {
                        $aliasOptions[$optionKey] = $this->stringToArray($optionValue);
                    }
                }
            }
            $output[$alias] = $aliasOptions;
        }
        return $output;
    }

/**
 * Converts formatted string to array
 *
 * A string formatted like 'Node.type:blog;' will be converted to
 * array('Node.type' => 'blog');
 *
 * @param string $string in this format: Node.type:blog;Node.user_id:1;
 * @return array
 */
    public function stringToArray($string) {
        $string = explode(';', $string);
        $stringArr = array();
        foreach ($string as $stringElement) {
            if ($stringElement != null) {
                $stringElementE = explode(':', $stringElement);
                if (isset($stringElementE['1'])) {
                    $stringArr[$stringElementE['0']] = $stringElementE['1'];
                } else {
                    $stringArr[] = $stringElement;
                }
            }
        }
        return $stringArr;
    }

}
