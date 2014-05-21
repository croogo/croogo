<?php

/**
 * StringConverter
 *
 * @package  Croogo.Croogo.Lib.Utility
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
namespace Croogo\Croogo\Utility;
class StringConverter {

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
					$value = $stringElementE['1'];
					if (strpos($value, ',') !== false) {
						$value = explode(',', $value);
					}
					$stringArr[$stringElementE['0']] = $value;
				} else {
					$stringArr[] = $stringElement;
				}
			}
		}
		return $stringArr;
	}

/**
 * Converts strings like controller:abc/action:xyz/ to arrays
 *
 * @param string|array $link link
 * @return array
 */
	public function linkStringToArray($link) {
		if (is_array($link)) {
			$link = key($link);
		}
		if (($pos = strpos($link, '?')) !== false) {
			parse_str(substr($link, $pos + 1), $query);
			$link = substr($link, 0, $pos);
		}
		$link = explode('/', $link);
		$prefixes = Configure::read('Routing.prefixes');
		$linkArr = array_fill_keys($prefixes, false);
		foreach ($link as $linkElement) {
			if ($linkElement != null) {
				$linkElementE = explode(':', $linkElement);
				if (isset($linkElementE['1'])) {
					if (in_array($linkElementE['0'], $prefixes)) {
						$linkArr[$linkElementE['0']] = strcasecmp($linkElementE['1'], 'false') === 0 ? false : true;
					} else {
						$linkArr[$linkElementE['0']] = urldecode($linkElementE['1']);
					}
				} else {
					$linkArr[] = $linkElement;
				}
			}
		}
		if (!isset($linkArr['plugin'])) {
			$linkArr['plugin'] = false;
		}

		if (isset($query)) {
			$linkArr['?'] = $query;
		}

		return $linkArr;
	}

/**
 * Converts array into string controller:abc/action:xyz/value1/value2?foo=bar
 *
 * @param array $url link
 * @return array
 */
	public function urlToLinkString($url) {
		$result = array();
		$actions = array_merge(array(
			'admin' => false, 'plugin' => false,
			'controller' => false, 'action' => false
			),
			$url
		);
		$queryString = null;
		foreach ($actions as $key => $val) {
			if (is_string($key)) {
				if (is_bool($val)) {
					if ($val === true) {
						$result[] = $key;
					}
				} elseif ($key == '?') {
					$queryString = '?' . http_build_query($val);
				} else {
					$result[] = $key . ':' . $val;
				}
			} else {
				$result[] = $val;
			}
		}
		return join('/', $result) . $queryString;
	}

/**
 * Extract the first paragraph from $text
 *
 * Options:
 *
 * - `tag` Wrap the returned value with a <p> tag. Default is `true`
 * - `regex` Regex expression to determine a paragraph
 * - `stripTags Strip all tags within the paragraph. Default is `true`
 * - `newline` Determine paragraph based on newlines instead of html <p> tag.
 *    Default is false
 *
 * @param string $text Html text
 * @param array $options Options
 * @return string
 */
	public function firstPara($text, $options = array()) {
		$paragraph = null;
		$options = array_merge(array(
			'tag' => false,
			'regex' => '#<p[^>]*>(.*)</p>#isU',
			'stripTags' => true,
			'newline' => false,
		), $options);

		if ($options['regex']) {
			preg_match($options['regex'], $text, $matches);
			if (isset($matches[1])) {
				$paragraph = $matches[1];
			}
		}

		if (empty($paragraph) && $options['newline']) {
			$paragraphs = preg_split('/\r\n|\r|\n/', $text);
			$paragraph = empty($paragraphs[0]) ? null : $paragraphs[0];
		}

		if ($paragraph) {
			if ($options['stripTags']) {
				$paragraph = strip_tags($paragraph);
			}
			if ($options['tag']) {
				$paragraph = '<p>' . $paragraph . '</p>';
			}
		}
		return $paragraph;
	}

}
