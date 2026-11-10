<?php
declare(strict_types=1);

namespace Croogo\Install\Middleware;

use Cake\Http\Response;
use Cake\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class InstallMiddleware
 *
 * Przekierowuje na instalator dopóki aplikacja nie jest zainstalowana.
 */
class InstallMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Middleware działa PRZED routingiem, więc decyzję podejmujemy po ścieżce URL,
        // nie po params['plugin'] (które są puste na tym etapie -> pętla redirectów).
        $path = $request->getUri()->getPath();
        $allowed = $this->isAllowedPath($path);
        if (!$allowed) {
            $url = [
                'plugin' => 'Croogo/Install',
                'controller' => 'Install',
                'action' => 'index',
            ];

            return (new Response())
                ->withStatus(307)
                ->withLocation(Router::url($url));
        }

        return $handler->handle($request);
    }

    /**
     * Ścieżki dopuszczone bez przekierowania na instalator (sam instalator + assety/debug).
     */
    protected function isAllowedPath(string $path): bool
    {
        $path = strtolower($path);
        if (strpos($path, '/install') === 0) {
            return true;
        }
        if (strpos($path, '/debug') !== false || strpos($path, '/debug-kit') !== false) {
            return true;
        }

        // assety statyczne (css/js/grafika/fonty)
        return (bool)preg_match('/\.(css|js|png|jpe?g|gif|svg|ico|woff2?|ttf|eot|map)$/', $path);
    }
}
