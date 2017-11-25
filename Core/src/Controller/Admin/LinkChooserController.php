<?php

namespace Croogo\Core\Controller\Admin;

use Croogo\Core\Croogo;
use Cake\Core\Configure;

class LinkChooserController extends AppController
{

    public function linkChooser()
    {
        Croogo::dispatchEvent('Controller.Links.setupLinkChooser', $this);
        $linkChoosers = Configure::read('Croogo.linkChoosers');
        $this->set(compact('linkChoosers'));
    }
}
