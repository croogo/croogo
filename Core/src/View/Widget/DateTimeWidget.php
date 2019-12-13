<?php
/**
 * Originally copyright FriendsOfCake from the friendsofcake/crud-view package.
 *
 */
namespace Croogo\Core\View\Widget;

use Cake\Chronos\ChronosInterface;
use Cake\Core\Configure;
use Cake\Database\Type;
use Cake\I18n\FrozenTime;
use Cake\I18n\I18n;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\View\Form\ContextInterface;
use Cake\View\Widget\DateTimeWidget as CakeDateTimeWidget;
use Croogo\Extensions\CroogoTheme;
use DateTime;
use DateTimeInterface;

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
        $class = isset($data['class']) ? $data['class'] : '';
        $required = $data['required'] ? 'required' : '';
        $role = isset($data['role']) ? $data['role'] : 'datetime-picker';
        $minDate = isset($data['data-mindate']) ? $data['data-mindate'] : null;
        $maxDate = isset($data['data-maxdate']) ? $data['data-maxdate'] : null;
        $format = null;
        $locale = I18n::getLocale();

        $timestamp = null;
        $timezoneOffset = null;

        if (isset($data['data-format'])) {
            $format = $this->_convertPHPToMomentFormat($data['data-format']);
        }

        if (!($val instanceof DateTimeInterface) && !empty($val)) {
            switch ($type) {
                case 'date':
                case 'time':
                    $val = Type::build($type)->marshal($val);
                    break;
                default:
                    $val = Type::build('datetime')->marshal($val);
            }
        }

        if ($val instanceof DateTimeInterface) {
            $val = $val->format(DateTime::ATOM);
        }

        $request = Router::getRequest();
        $timezone = $request->getSession()->read('Auth.User.timezone');
        if (!$timezone) {
            $timezone = 'UTC';
        }

        if (!$format) {
            $format = $type === 'date' ? 'L' : 'L LT';
        }

        $widget = <<<html
            <div class="input-group $type $class" 
                data-target-input="nearest"
                id="{$id}"
                role="$role"
                data-timezone="$timezone"
                data-locale="$locale"
                data-format="$format"
                data-minDate="$minDate"
                data-maxDate="$maxDate"
            >
                <input
                    type="text"
                    class="form-control datetimepicker-input"
                    name="{$name}"
                    value="{$val}"
                    data-target="#{$id}"
                    data-toggle="datetimepicker"
                    $required
                />
html;

        $addon = isset($data['addon']) ? $data['addon'] : true;
        if ($addon) {
            $themeData = CroogoTheme::config(Configure::read('Site.admin_theme'));
            $iconSet = Hash::extract($themeData, 'settings.iconDefaults.iconSet')[0];
            $widget .= <<<html
                <div class="input-group-append" data-target="#{$id}" data-toggle="datetimepicker">
                    <span class="input-group-text">
                    <i class="$iconSet fa-calendar"></i>
                    </span>
                </div>
html;
        }

        $widget .= <<<html
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
