<?php

class AccountModel {
	private $adminUsername;
	private $adminPassword;
	public $notifications;
	private $notify;
	private $accountDAL;
	public $token;
	public $tokenExpiration;

	public function __construct(Notify $notify) {
		//Notifications notify->success/error/info(message, optional header)
		$this->notify = $notify;
		$username = 'Admin';
		$password = 'Password';

		$this->adminUsername = $username;
		$this->adminPassword = crypt($password, $username);

		$this->accountDAL = new AccountDAL();
	}

	public function getUsername() {
		if (isset($_SESSION['username'])) {
			return $_SESSION['username'];
		} else {
			return '';
		}
	}

	public function logout() {
		session_destroy();
		session_start();
		$this->notify->info('Du är nu utloggad.');
	}

	//Is user already logged in?
	public function IsLoggedIn($userAgent) {
		if (!isset($_SESSION['username'])) {
			return false;
		}

		$username = $_SESSION['username'];
		if ($username == $this->adminUsername && $_SESSION['userAgent'] == $userAgent) {
			return true;
		} else {
			return false;
		}
	}

	//Token validation, used for remembering users
	public function validateToken($token, $userAgent) {
		$result = $this->accountDAL->findRememberedUser($token);
		if ($result != false) {
			$_SESSION['username'] = $result;
			$_SESSION['userAgent'] = $userAgent;
			$this->notify->success('Inloggning lyckades via cookies.');
			return true;
		}
		
		$this->notify->error('Felaktig information i cookie.');
		return false;
	}

	//Validate credentials, used on login by post
	public function validateCredentials($username, $password, $remember, $userAgent) {
		if ($username == '') {
			$this->notify->error('Användarnamn saknas.');
			return false;
		}

		if ($password == '') {
			$this->notify->error('Lösenord saknas.');
			return false;
		}

		if ($username != $this->adminUsername || $password != $this->adminPassword) {
			$this->notify->error('Felaktigt användarnamn och/eller lösenord');
			return false;
		}

		//Remember user?
		if ($remember == true) {
			$this->rememberUser($username);
			$this->notify->success('Inloggning lyckades och vi kommer ihåg dig nästa gång.');
		} else {
			$this->notify->success('Inloggning lyckades.');
		}

		$_SESSION['username'] = $username;

		//Basic session stealing prevention (too basic)
		$_SESSION['userAgent'] = $userAgent;
		return true;
	}

	//Remember user, saves token
	private function rememberUser($username) {
		//Set expiration to 30 days from now
		$expiration = time()+60;
		$token = $this->createToken($username, $expiration);
		$this->accountDAL->rememberUser($token, $username, $expiration);

		//We'll need token and expiration in view
		$this->token = $token;
		$this->tokenExpiration = $expiration;
	}

	//Generate token
	private function createToken($username, $expiration) {
		return crypt($username, $expiration . 'secret string appended to make it harder to calculate');
	}
}