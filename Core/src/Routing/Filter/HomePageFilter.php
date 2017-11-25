<?php

namespace Croogo\Core\Routing\Filter;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Routing\DispatcherFilter;
use Croogo\Core\Utility\StringConverter;

/**
 * Class HomePageFilter
 */
class HomePageFilter extends DispatcherFilter
{
    /**
     * Priority setting.
     *
     * This filter should be run just after the routing filter
     *
     * @var int
     */
    protected $_priority = 15;

    /**
     * Applies Routing and additionalParameters to the request to be dispatched.
     * If Routes have not been loaded they will be loaded, and config/routes.php will be run.
     *
     * @param \Cake\Event\Event $event containing the request, response and additional params
     * @return \Cake\Network\Response|null A response will be returned when a redirect route is encountered.
     */
    public function beforeDispatch(Event $event)
    {
        $request = $event->data['request'];
        if ($request->here !== $request->webroot || $request->param('prefix') === 'admin') {
            return;
        }

        $homeUrl = Configure::read('Site.home_url');
        if ($homeUrl && strpos($homeUrl, ':') !== false) {
            $converter = new StringConverter();
            $url = $converter->linkStringToArray($homeUrl);
            $request->addParams($url);
        }
    }
}
