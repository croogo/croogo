<?php

namespace Croogo\Core\View\Cell\Admin;

use Cake\Core\Configure;
use Cake\View\Cell;
use Croogo\Core\Croogo;

/**
 * Class LinkChooserCell
 */
class LinkChooserCell extends Cell
{
    public function display($target)
    {
        Croogo::dispatchEvent('Controller.Links.setupLinkChooser', $this);
        $linkChoosers = Configure::read('Croogo.linkChoosers');
        $this->set(compact('target', 'linkChoosers'));
    }
}
