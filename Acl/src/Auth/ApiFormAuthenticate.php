<?php

namespace Croogo\Acl\Auth;

use Cake\Auth\FormAuthenticate;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\Exception\ForbiddenException;

class ApiFormAuthenticate extends FormAuthenticate
{

    public function unauthenticated(ServerRequest $request, Response $response) {
        throw new ForbiddenException($this->_config['authError']);
    }

}
