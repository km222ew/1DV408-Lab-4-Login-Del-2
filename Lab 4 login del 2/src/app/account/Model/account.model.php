<?php

require_once("account.repository.php");

class AccountModel {

	public $notifications;
	public $tokenExpiration;

    private $notify;
    private $token;

	public function __construct(Notify $notify)
    {
		//Notifications notify->success/error/info(message, optional header)
		$this->notify = $notify;
        $this->userRep = new AccountUserRepository();
	}

	public function getUsername()
    {
		if (isset($_SESSION['username']))
        {
			return $_SESSION['username'];
		}
        else
        {
			return '';
		}
	}

    //Look for user in database
    public function getUserFromDb($username)
    {
        $user = $this->userRep->getUserByName($username);

        return $user;
    }

	public function logout()
    {
		session_destroy();
		session_start();
		$this->notify->info('Du är nu utloggad.');
	}

	//Is user already logged in?
	public function IsLoggedIn($userAgent, $userIp)
    {
		if (!isset($_SESSION['username']))
        {
			return false;
		}

		$username = $_SESSION['username'];

        $user = $this->getUserFromDb($username);

        if($user != null && $_SESSION['userAgent'] == $userAgent && $_SESSION['userIp'] == $userIp)
        {
            return true;
        }
        else
        {
            return false;
        }
	}

    public function setSession($username, $userIp, $userAgent)
    {
        $_SESSION['username'] = $username;
        $_SESSION['userIp'] = $userIp;
        $_SESSION['userAgent'] = $userAgent;
    }

	//Validate credentials, used on login by post
	public function validateCredentials($username, $password, $userAgent, $userIp, $remember) {
		if ($username == '')
        {
			$this->notify->error('Användarnamn saknas.');
			return false;
		}

		if ($password == '')
        {
			$this->notify->error('Lösenord saknas.');
			return false;
		}

        $user = $this->getUserFromDb($username);

        if($user == null || $password != $user->getPassword())
        {
            $this->notify->error('Felaktigt användarnamn och/eller lösenord');
            return false;
        }

        if($remember)
        {
            $this->notify->success('Inloggning lyckades och vi kommer ihåg dig nästa gång.');
        }
        else
        {
            $this->notify->success('Inloggning lyckades.');
        }

        $this->setSession($username, $userIp, $userAgent);

		return true;
	}

    public function loginWithCookies($usernameCookie, $tokenPassCookie, $userIP, $userAgent)
    {
        $user = $this->getUserFromDb($usernameCookie);

        if($user != null && $user->getToken() == $tokenPassCookie && $user->getExpire() > time())
        {
            $this->setSession($usernameCookie, $userIP, $userAgent);

            $this->notify->success('Inloggning lyckades via cookies');

            return true;
        }
        else
        {
            $this->notify->error('Felaktig information i cookie');
            return false;
        }
    }

	//Saves token and cookie expiration
	public function rememberUser($expiration, $username)
    {
        $this->userRep->updateUserIdentifier($this->token, $expiration, $username);
	}

    //Generate, set, and return new token
    public function getToken()
    {
        $this->token = sha1(rand().microtime());

        return $this->token;
    }
}