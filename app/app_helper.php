<?php
class AppHelper extends Helper {

    function url($url = null, $full = false) {
        if (!isset($url['locale']) && isset($this->params['locale'])) {
            $url['locale'] = $this->params['locale'];
        }
        return parent::url($url, $full);
    }

}
?>