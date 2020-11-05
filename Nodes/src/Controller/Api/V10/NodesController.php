<?php
declare(strict_types=1);

namespace Croogo\Nodes\Controller\Api\V10;

use Cake\Event\EventInterface;
use Croogo\Core\Utility\StringConverter;
use Croogo\Core\Controller\Api\AppController;

/**
 * Nodes Controller
 *
 * @property \Croogo\Nodes\Model\Table\NodesTable $Nodes
 * @property \Croogo\Core\Controller\Component\CroogoComponent $Croogo
 * @property \Croogo\Meta\Controller\Component\MetaComponent $Meta
 * @property \Croogo\Blocks\Controller\Component\BlocksComponent $BlocksHook
 * @property \Croogo\Acl\Controller\Component\FilterComponent $Filter
 * @property \Acl\Controller\Component\AclComponent $Acl
 * @property \Croogo\Core\Controller\Component\ThemeComponent $Theme
 * @property \Croogo\Acl\Controller\Component\AccessComponent $Access
 * @property \Croogo\Settings\Controller\Component\SettingsComponent $SettingsComponent
 * @property \Croogo\Nodes\Controller\Component\NodesComponent $NodesHook
 * @property \Croogo\Menus\Controller\Component\MenuComponent $Menu
 * @property \Croogo\Users\Controller\Component\LoggedInUserComponent $LoggedInUser
 * @property \Croogo\Taxonomy\Controller\Component\TaxonomyComponent $Taxonomy
 * @property \Search\Controller\Component\SearchComponent $Search
 * @property \Croogo\Core\Controller\Component\BulkProcessComponent $BulkProcess
 * @property \Croogo\Core\Controller\Component\RecaptchaComponent $Recaptcha
 */
class NodesController extends AppController
{

    public function index()
    {
        $this->Crud->on('afterPaginate', function (EventInterface $event) {
            $entities = $event->getSubject()->entities;
            $stringConverter = new StringConverter();
            foreach ($entities as $entity) {
                if (empty($entity->excerpt)) {
                    $entity->excerpt = $stringConverter->firstPara($entity->body);
                }
            }
        });
        return $this->Crud->execute();
    }

    public function view()
    {
        return $this->Crud->execute();
    }

    public function lookup()
    {
        // FIXME: Things get broken when Translate is activated
        $this->Nodes->behaviors()->reset();
        $this->Nodes->addBehavior('Search.Search');
        $this->Nodes->associations()->remove('I18n');

        return $this->Crud->execute();
    }
}
