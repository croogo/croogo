<?php
/**
 * Originally copyright FriendsOfCake from the friendsofcake/crud-view package.
 *
 */
namespace Croogo\Core\View\Widget;

use Cake\Core\Configure;
use Cake\I18n\I18n;
use Cake\I18n\Time;
use Cake\View\Form\ContextInterface;
use Cake\View\Widget\DateTimeWidget as CakeDateTimeWidget;
use DateTime;

class DateTimeWidget extends CakeDateTimeWidget
{

    /**
     * Renders a date time widget.
     *
     * @param array $data Data to render with.
     * @param \Cake\View\Form\ContextInterface $context The current form context.
     * @return string A generated select box.
     * @throws \RuntimeException When option data is invalid.
     */
    public function render(array $data, ContextInterface $context)
    {
        $id = $data['id'];
        $name = $data['name'];
        $val = $data['val'];
        $type = $data['type'];
        $required = $data['required'] ? 'required' : '';
        $role = isset($data['role']) ? $data['role'] : 'datetime-picker';
        $format = null;
        $locale = I18n::locale();

        $timestamp = null;
        $timezoneOffset = null;

        if (isset($data['data-format'])) {
            $format = $this->_convertPHPToMomentFormat($data['data-format']);
        }

        if (!($val instanceof DateTime) && !empty($val)) {
            $val = $type === 'date' ? Time::parseDate($val) : Time::parseDateTime($val);
        }

        if (!empty($val)) {
            $val = $val->format($type === 'date' ? 'Y-m-d' : 'Y-m-d H:i:s');
        }

        if (!$format) {
            $format = $type === 'date' ? 'L' : 'L LT';
        }

        $widget = <<<html
            <div class="input-group $type">
                <input
                    type="text"
                    class="form-control"
                    name="$name"
                    value="$val"
                    id="$id"
                    role="$role"
                    data-locale="$locale"
                    data-format="$format"
                    $required
                />
                <label for="$id" class="input-group-addon">
                    <span class="fa fa-calendar"></span>
                </label>
            </div>
html;

        return $widget;
    }

    /**
     * Converts PHP date format to one supported by MomentJS.
     *
     * @param string $format PHP date format.
     * @return string MomentJS date format.
     * @see http://stackoverflow.com/a/30192680
     */
    protected function _convertPHPToMomentFormat($format)
    {
        $replacements = [
            'd' => 'DD',
            'D' => 'ddd',
            'j' => 'D',
            'l' => 'dddd',
            'N' => 'E',
            'S' => 'o',
            'w' => 'e',
            'z' => 'DDD',
            'W' => 'W',
            'F' => 'MMMM',
            'm' => 'MM',
            'M' => 'MMM',
            'n' => 'M',
            't' => '', // no equivalent
            'L' => '', // no equivalent
            'o' => 'YYYY',
            'Y' => 'YYYY',
            'y' => 'YY',
            'a' => 'a',
            'A' => 'A',
            'B' => '', // no equivalent
            'g' => 'h',
            'G' => 'H',
            'h' => 'hh',
            'H' => 'HH',
            'i' => 'mm',
            's' => 'ss',
            'u' => 'SSS',
            'I' => '', // no equivalent
            'O' => '', // no equivalent
            'P' => '', // no equivalent
            'T' => '', // no equivalent
            'Z' => '', // no equivalent
            'c' => '', // no equivalent
            'r' => '', // no equivalent
            'U' => 'X',
        ];
        $momentFormat = strtr($format, $replacements);

        return $momentFormat;
    }

    /**
     * Returns a list of fields that need to be secured for this widget.
     *
     * When the hour picker is in 24hr mode (null or format=24) the meridian
     * picker will be omitted.
     *
     * @param array $data The data to render.
     * @return array Array of fields to secure.
     */
    public function secureFields(array $data)
    {
        if (!isset($data['name']) || $data['name'] === '') {
            return [];
        }

        return [$data['name']];
    }
}
