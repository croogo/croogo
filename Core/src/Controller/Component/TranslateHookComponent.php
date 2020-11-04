<?php
declare(strict_types=1);

namespace Croogo\Core\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Plugin;
use Cake\Event\EventInterface;
use Cake\I18n\I18n;

/**
 * Add support for i18n in API
 *
 * We could not use Croogo::hookComponent() as it is not supported for controllers with Api prefix.
 */
class TranslateHookComponent extends Component
{

    public function initialize(array $config): void
    {
        parent::initialize($config);

        if (Plugin::isLoaded('Croogo/Translate')) {
            $this->getController()->Crud->on('beforePaginate', [$this, '_crudBeforePaginate']);
        }
    }

    public function _crudBeforePaginate(EventInterface $event) {
        $controller = $this->getController();
        $locale = $controller->getRequest()->getQuery('locale');
        if (!$locale) {
            return;
        }

        I18n::setlocale($locale);
        /** @var \Cake\ORM\Table */
        $Model = $controller->loadModel();
        $Model->setLocale($locale);
        foreach ($Model->associations() as $association) {
            $target = $association->getTarget();
            if ($target->hasBehavior('Translate')) {
                $target->setLocale($locale);
            }
            foreach ($target->associations() as $innerAssociation) {
                $innerTarget = $innerAssociation->getTarget();
                if ($innerTarget->hasBehavior('Translate')) {
                    $innerTarget->setLocale($locale);
                }
            }
        }
    }

}