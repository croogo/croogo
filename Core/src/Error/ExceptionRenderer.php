<?php

namespace Croogo\Core\Error;

use Cake\Controller\Controller;
use Cake\Core\App;
use Cake\Error\ExceptionRenderer as CakeExceptionRenderer;
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\Http\ServerRequestFactory;
use Cake\Routing\Router;
use Exception;

/**
 * Class ExceptionRenderer
 */
class ExceptionRenderer extends CakeExceptionRenderer
{
    protected function _getController()
    {
        if (!$request = Router::getRequest(true)) {
            $request = ServerRequestFactory::fromGlobals();
        }
        $response = new Response();

        try {
            $class = App::className('Croogo/Core.Error', 'Controller', 'Controller');
            $controller = new $class($request, $response);
            $controller->startupProcess();
            $startup = true;
        } catch (Exception $e) {
            $startup = false;
        }

        // Retry RequestHandler, as another aspect of startupProcess()
        // could have failed. Ignore any exceptions out of startup, as
        // there could be userland input data parsers.
        if ($startup === false && !empty($controller) && isset($controller->RequestHandler)) {
            try {
                $event = new Event('Controller.startup', $controller);
                $controller->RequestHandler->startup($event);
            } catch (Exception $e) {
            }
        }
        if (empty($controller)) {
            $controller = new Controller($request, $response);
            $controller->viewBuilder()->className('Croogo/Core.Croogo');
        }

        return $controller;
    }
}
