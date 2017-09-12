<?php

namespace Croogo\Core\Error;

use Cake\Core\App;
use Cake\Error\ExceptionRenderer as CakeExceptionRenderer;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Routing\Router;
use CrudJsonApi\Error\JsonApiExceptionRenderer;

/**
 * Class ExceptionRenderer
 */
class ExceptionRenderer extends JsonApiExceptionRenderer
{
    protected function _getController()
    {
        if (!$request = Router::getRequest(true)) {
            $request = Request::createFromGlobals();
        }
        $response = new Response();

        try {
            $class = App::className('Croogo/Core.Error', 'Controller', 'Controller');
            $controller = new $class($request, $response);
            $controller->startupProcess();
            $startup = true;
        } catch (\Exception $e) {
            $startup = false;
        }

        if ($startup === false && !empty($controller) && isset($controller->RequestHandler)) {
            $controller = parent::_getController();
        }

        return $controller;
    }
}
