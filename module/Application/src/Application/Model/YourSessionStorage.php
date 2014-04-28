<?php
/**
 * @module Application
 * @desc  session storage to keep the zfcuser session and time it out somewhere
 * @package Application/src/Application/Model/YourSessionStorage.php
 * @author Yassine Nachti <nachtis@gmail.com>
 */

namespace Application\Model;

use Zend\Authentication\Storage\Session as SessionStorage;

class YourSessionStorage extends SessionStorage
{
	/**
	 * Return session manager for customization
	 */
	public function getSessionManager() {		
		return $this->session->getManager();
	}
}