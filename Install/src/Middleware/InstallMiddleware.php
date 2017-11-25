<?php

namespace Croogo\Install\Middleware;

use Cake\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * Class InstallMiddleware
 */
class InstallMiddleware
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        if (strpos($request->getUri()->getPath(), 'install') === false &&
            strpos($request->getUri()->getPath(), 'debug_kit') === false
         ) {
            $url = ['plugin' => 'Croogo/Install', 'controller' => 'Install', 'action' => 'index'];

            return new RedirectResponse(Router::url($url), 307);
        }
        return $next($request, $response);
    }
}
