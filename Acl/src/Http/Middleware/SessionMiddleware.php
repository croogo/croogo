<?php
declare(strict_types=1);

namespace Croogo\Acl\Http\Middleware;

use Cake\Core\Configure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionMiddleware implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (Configure::read('Access Control.splitSession')) {
            /** @var \Cake\Http\Request $request */
            $cookiePath = strtolower($request->getAttribute('base') . '/' . $request->getParam('prefix'));
            ini_set('session.cookie_path', $cookiePath);
        }

        return $handler->handle($request);
    }

}