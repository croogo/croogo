<?php

namespace Croogo\Core\View;

use App\View\AppView;

class CroogoView extends AppView
{

    public function initialize()
    {
        parent::initialize();
        if ($this->request->param('prefix') == 'admin') {
            $this->loadHelper('Croogo/Core.Croogo');
            $this->loadHelper('Html', ['className' => 'Croogo/Core.CroogoHtml']);
            $this->loadHelper('Form', ['className' => 'Croogo/Core.CroogoForm']);
            $this->loadHelper('Paginator', ['className' => 'Croogo/Core.CroogoPaginator']);
        }
    }
}
