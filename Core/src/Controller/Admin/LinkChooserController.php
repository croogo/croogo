<?php

namespace Croogo\Core\Controller\Admin;

use Cake\Core\Configure;
use Croogo\Core\Croogo;

class LinkChooserController extends AppController
{

    public function linkChooser()
    {
        Croogo::dispatchEvent('Controller.Links.setupLinkChooser', $this);
        $linkChoosers = Configure::read('Croogo.linkChoosers');
        $this->set(compact('linkChoosers'));
    }
}
