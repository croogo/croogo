<?php

namespace Croogo\Settings\Setting;

/**
 * Class TimezonesSetting
 */
class TimezonesSetting
{
    public function __invoke()
    {
        $continents = [
            'Africa', 'America', 'Antarctica', 'Artic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific'
        ];
        $zones = timezone_identifiers_list();
        $locations = [];
        foreach ($zones as $zone) {
            if (strpos($zone, '/') === false) {
                $locations[$zone] = $zone;
                continue;
            }
            list($continent, $city) = explode('/', $zone); // 0 => Continent, 1 => City

            // Only use "friendly" continent names
            if (in_array($continent, $continents)) {
                if (isset($city) != '') {
                    $locations[$continent][$continent . '/' . $city] = str_replace('_', ' ', $city); // Creates array(DateTimeZone => 'Friendly name')
                }
            }
        }

        return $locations;
    }
}
