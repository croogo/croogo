<?php
/**
 * Javascript Generator class file.
 *
 * This is deprecated and only here to easy migration from 2.x applications
 * that rely on the JsHelper buffer functionality.
 */
namespace Croogo\Core\View\Helper;

use Cake\View\Helper;

/**
 * Javascript Generator helper class for easy use of JavaScript.
 *
 * JsHelper provides an abstract interface for authoring JavaScript with a
 * given client-side library.
 *
 * Note: onDomReady always defaults to false now, you will to add this yourself.
 */
class JsHelper extends Helper
{

/**
 * Whether or not you want scripts to be buffered or output.
 *
 * @var bool
 */
    public $bufferScripts = true;

/**
 * Helper dependencies
 *
 * @var array
 */
    public $helpers = ['Html', 'Form'];

/**
 * Variables to pass to Javascript.
 *
 * @var array
 * @see JsHelper::set()
 */
    protected $_jsVars = [];

/**
 * Scripts that are queued for output
 *
 * @var array
 * @see JsHelper::buffer()
 */
    protected $_bufferedScripts = [];

/**
 * The javascript variable created by set() variables.
 *
 * @var string
 */
    public $setVariable = 'app';

/**
 * Generates a JavaScript object in JavaScript Object Notation (JSON)
 * from an array. Will use native JSON encode method if available, and $useNative == true
 *
 * ### Options:
 *
 * - `prefix` - String prepended to the returned data.
 * - `postfix` - String appended to the returned data.
 *
 * @param array $data Data to be converted.
 * @param array $options Set of options, see above.
 * @return string A JSON code block
 */
    public function object($data = [], $options = [])
    {
        $defaultOptions = [
            'prefix' => '', 'postfix' => '',
        ];
        $options += $defaultOptions;

        return $options['prefix'] . json_encode($data) . $options['postfix'];
    }

/**
 * Writes all Javascript generated so far to a code block or
 * caches them to a file and returns a linked script. If no scripts have been
 * buffered this method will return null. If the request is an XHR(ajax) request
 * onDomReady will be set to false. As the dom is already 'ready'.
 *
 * ### Options
 *
 * - `inline` - Set to true to have scripts output as a script block inline
 *   if `cache` is also true, a script link tag will be generated. (default true)
 * - `cache` - Set to true to have scripts cached to a file and linked in (default false)
 * - `clear` - Set to false to prevent script cache from being cleared (default true)
 * - `onDomReady` - wrap cached scripts in domready event (default true)
 * - `safe` - if an inline block is generated should it be wrapped in <![CDATA[ ... ]]> (default true)
 *
 * @param array $options options for the code block
 * @return mixed Completed javascript tag if there are scripts, if there are no buffered
 *   scripts null will be returned.
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/js.html#JsHelper::writeBuffer
 */
    public function writeBuffer($options = [])
    {
        $defaults = [
            'cache' => false, 'clear' => true, 'safe' => true
        ];
        $options += $defaults;
        $script = implode("\n", $this->getBuffer($options['clear']));

        if (empty($script)) {
            return null;
        }

        $opts = $options;
        unset($opts['cache'], $opts['clear']);

        if (isset($opts['inline'])) {
            unset($opts['inline']);
        }
        $return = $this->Html->scriptBlock($script, $opts);

        return $return;
    }

/**
 * Write a script to the buffered scripts.
 *
 * @param string $script Script string to add to the buffer.
 * @param bool $top If true the script will be added to the top of the
 *   buffered scripts array. If false the bottom.
 * @return void
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/js.html#JsHelper::buffer
 */
    public function buffer($script, $top = false)
    {
        if ($top) {
            array_unshift($this->_bufferedScripts, $script);
        } else {
            $this->_bufferedScripts[] = $script;
        }
    }

/**
 * Get all the buffered scripts
 *
 * @param bool $clear Whether or not to clear the script caches (default true)
 * @return array Array of scripts added to the request.
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/js.html#JsHelper::getBuffer
 */
    public function getBuffer($clear = true)
    {
        $this->_createVars();
        $scripts = $this->_bufferedScripts;
        if ($clear) {
            $this->_bufferedScripts = [];
            $this->_jsVars = [];
        }
        return $scripts;
    }

/**
 * Generates the object string for variables passed to javascript and adds to buffer
 *
 * @return void
 */
    protected function _createVars()
    {
        if (!empty($this->_jsVars)) {
            $setVar = (strpos($this->setVariable, '.')) ? $this->setVariable : 'window.' . $this->setVariable;
            $this->buffer($setVar . ' = ' . $this->object($this->_jsVars) . ';', true);
        }
    }

/**
 * Pass variables into Javascript. Allows you to set variables that will be
 * output when the buffer is fetched with `JsHelper::getBuffer()` or `JsHelper::writeBuffer()`
 * The Javascript variable used to output set variables can be controlled with `JsHelper::$setVariable`
 *
 * @param string|array $one Either an array of variables to set, or the name of the variable to set.
 * @param string|array $two If $one is a string, $two is the value for that key.
 * @return void
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/js.html#JsHelper::set
 */
    public function set($one, $two = null)
    {
        $data = null;
        if (is_array($one)) {
            if (is_array($two)) {
                $data = array_combine($one, $two);
            } else {
                $data = $one;
            }
        } else {
            $data = [$one => $two];
        }
        if (!$data) {
            return false;
        }
        $this->_jsVars = array_merge($this->_jsVars, $data);
    }
}
