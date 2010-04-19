<?php
/**
 * AppError
 *
 * PHP version 5
 *
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AppError extends ErrorHandler {
/**
 * securityError
 *
 * @return void
 */
    public function securityError() {
        $this->controller->set(array(
            'referer' => $this->controller->referer(),
        ));
        $this->_outputMessage('security');
    }
}
?>