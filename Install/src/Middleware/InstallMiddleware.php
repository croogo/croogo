<?php
declare(strict_types=1);

namespace Croogo\Install\Middleware;

use Cake\Routing\Router;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class InstallMiddleware
 */
class InstallMiddleware
{

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        /** @var \Cake\Http\ServerRequest $request */
        $plugin = $request->getParam('plugin');
        if (!in_array($plugin, ['Croogo/Install', 'DebugKit'])) {
            $url = [
                'plugin' => 'Croogo/Install',
                'controller' => 'Install',
                'action' => 'index',
            ];

            return new RedirectResponse(Router::url($url), 307);
        }

        return $next($request, $response);
    }
}
