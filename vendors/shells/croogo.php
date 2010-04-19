<?php
/**
 * Croogo Shell
 *
 * PHP version 5
 *
 * @category Shell
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
App::import('Core', 'Security');
class CroogoShell extends Shell {
/**
 * Get hashed password
 *
 * Usage: ./cake croogo password myPasswordHere
 */
    public function password() {
        $value = trim($this->args[0]);
        $this->out(Security::hash($value, null, true));
    }
    
}
?>