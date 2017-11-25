<?php

namespace Croogo\Core\Shell;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;

use Croogo\Acl\AclGenerator;

/**
 * Croogo Shell
 *
 * @category Shell
 * @package  Croogo.Croogo.Console.Command
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoShell extends AppShell
{

    public $tasks = [
        'Croogo/Core.Upgrade',
    ];

/**
 * Display help/options
 */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->description(__d('croogo', 'Croogo Utilities'))
            ->addSubCommand('upgrade', [
                'help' => __d('croogo', 'Upgrade Croogo'),
                'parser' => $this->Upgrade->getOptionParser(),
            ])
            ->addSubcommand('password', [
                'help' => 'Get hashed password',
                'parser' => [
                    'description' => 'Get hashed password',
                    'arguments' => [
                        'password' => [
                            'required' => true,
                            'help' => 'Password to hash',
                        ],
                    ],
                ],
            ])->addSubcommand('sync_content_acos', [
                'help' => 'Sync content acos',
                'parser' => [
                    'description' => 'Populate acos of existing contents',
                ],
            ]);
        return $parser;
    }

/**
 * Get hashed password
 *
 * Usage: ./Console/cake croogo password myPasswordHere
 */
    public function password()
    {
        $value = trim($this->args['0']);
        $this->out(Security::hash($value, null, true));
    }

    public function syncContentAcos()
    {
        $aclGenerator = new AclGenerator();
        return $aclGenerator->syncContentAcos();
    }

}
