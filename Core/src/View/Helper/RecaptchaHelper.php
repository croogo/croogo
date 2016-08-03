<?php

namespace Croogo\Core\View\Helper;

use Cake\Core\Configure;
use Cake\View\Helper;

/**
 * Recaptcha Helper
 *
 * @package Croogo.Core.View.Helper
 */
class RecaptchaHelper extends Helper
{

    /**
     * secure API Url
     */
    const SECURE_API_URL = 'https://www.google.com/recaptcha/api.js';

    /**
     * helpers
     */
    public $helpers = ['Html', 'Form', 'Js'];

    /**
     * beforeRender
     */
    public function beforeRender($viewFile)
    {
        if ($this->request->is('ajax')) {
            return;
        }
        if ($this->request->param('prefix') === 'admin') {
            return;
        }
        $this->Html->script(self::SECURE_API_URL, ['block' => true]);
    }

    /**
     * render
     *
     * @data-type: audio | image
     */
    public function display($options = [])
    {
        $_defaults = [
            'data-sitekey' => Configure::read('Recaptcha.pubKey'),
        ];
        $options = array_merge($_defaults, $options);

        $div = $this->Html->div('g-recaptcha', '', $options);

        $this->Form->unlockField('g-recaptcha-response');

        return $div;
    }
}
