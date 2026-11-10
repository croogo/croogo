<?php
declare(strict_types=1);

namespace Acl;

use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;

/**
 * Plugin `Acl` — zvendorowany shim cakephp/acl 0.9 pod CakePHP 5.
 *
 * Rejestruje domyślną konfigurację (odpowiednik config/bootstrap.php
 * oryginalnego pluginu). Reszta pluginu (routes/middleware/console) pusta.
 */
class AclPlugin extends BasePlugin
{
    protected ?string $name = 'Acl';

    public function bootstrap(PluginApplicationInterface $app): void
    {
        if (!Configure::read('Acl.classname')) {
            Configure::write('Acl.classname', 'DbAcl');
        }
        if (!Configure::read('Acl.database')) {
            Configure::write('Acl.database', 'default');
        }
    }
}
