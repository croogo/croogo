<?php
if (!empty($this->Breadcrumbs->getCrumbs())) {
    $this->Breadcrumbs->setTemplates([
        'item' => '<li class="breadcrumb-item" {{attrs}}><a href="{{url}}"{{innerAttrs}}>{{title}}</a></li>{{separator}}',
        'itemWithoutLink' => '<li class="breadcrumb-item" {{attrs}}><span{{innerAttrs}}>{{title}}</span></li>{{separator}}',
    ]);

    $this->Breadcrumbs->prepend($this->Html->icon('home'), '/admin', ['escape' => false]);
    $crumbs = $this->Breadcrumbs->render([
        'class' => 'breadcrumb m-0',
    ]);

    echo $this->Html->tag('nav', $crumbs, [
        'id' => 'breadcrumb-container',
        'class' => 'd-none d-md-block',
    ]);
}
