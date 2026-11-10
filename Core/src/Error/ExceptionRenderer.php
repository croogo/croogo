<?php

namespace Croogo\Core\Error;

use Cake\Controller\Controller;
use Cake\Core\App;
use Cake\Error\Renderer\WebExceptionRenderer;
use Cake\Http\ServerRequestFactory;
use Cake\Routing\Router;
use Exception;

/**
 * Class ExceptionRenderer
 */
class ExceptionRenderer extends WebExceptionRenderer
{
    protected function _getController(): Controller
    {
        // Cake 5: Router::getRequest() bez argumentu; Controller przyjmuje tylko request.
        $request = Router::getRequest();
        if (!$request) {
            $request = ServerRequestFactory::fromGlobals();
        }

        try {
            $class = App::className('Croogo/Core.Error', 'Controller', 'Controller');
            /** @var \Cake\Controller\Controller $controller */
            $controller = new $class($request);
            $controller->startupProcess();
        } catch (Exception $e) {
        }

        if (empty($controller)) {
            $controller = new Controller($request);
            $controller->viewBuilder()->setClassName('Croogo/Core.Croogo');
        }

        return $controller;
    }
}
