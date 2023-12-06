<?php
namespace OCA\FALog;

use OC\User\Session;
use OCP\ISession;


class CurrentUser {
    private Session $userSession;
    private ISession $session;
    public function __construct() {
        $this->userSession = \OC::$server->getUserSession();
        $this->session = $this->userSession->getSession();
    }

    public function getUserType(): string {
        return $this->getUserId() ? 'loggedIn' : 'public';
    }

    public function getUserId(): ?string {
        return $this->session->exists('user_id') ? $this->session->get('user_id') : null;
    }

    public function getIp(): String {
        $ip = "";
		// if user from the share internet   
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		//if user is from the proxy   
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		//if user is from the remote address   
		else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

        return $ip;
    }

    public function getAllheaders(): string {
        return (string)json_encode(getallheaders());
    }
}
?>