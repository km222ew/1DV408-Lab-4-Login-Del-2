<?php

class AccountView {

	private $model;
	public $cookieService;

	//Get username from post
	public function getUsername() {
		if (isset($_POST['username'])) {
			//Save username in cookie to remember input
			$this->cookieService->save('username', $_POST['username']);
			return $_POST['username'];
		}
		
		return '';
	}

	//Get password from post
	public function getPassword() {
		if (isset($_POST['password']) && $_POST['password'] != '') {
			return $password = crypt($_POST['password'], $this->getUsername());
		}

		return '';
	}

	//Remember user?
	public function getRemember() {
		if (isset($_POST['remember'])) {
			return $_POST['remember'];
		} else {
			return false;
		}
	}

	//Remember user, sets cookie with token and expiration
	public function remember() {
		$token = $this->model->token;
		$expiration = $this->model->tokenExpiration;
		$this->cookieService->saveToken($token, $expiration);
	}

	//Get token from cookie
	public function getToken() {
		if ($this->cookieService->loadToken() != '') {
			return $this->cookieService->loadToken();
		} else {
			return '';
		}
	}

	//Remove token, remove on fail
	public function removeToken() {
		$this->cookieService->remove('token');
	}

	//Get client browser info
	public function getUserAgent() {
		return $_SERVER['HTTP_USER_AGENT'];
	}

	public function __construct(AccountModel $model, CookieService $cookieService) {
		$this->model = $model;
		$this->cookieService = $cookieService;
	}

	//Did user request login?
	public function didLogin() {
		if (isset($_POST['login'])) {
			return true;
		} else {
			return false;
		}
	}

	//Did user request to be logged out?
	public function didLogout() {
		if (isset($_GET['action']) && $_GET['action'] == 'logout') {
			$this->cookieService->remove('token');
			return true;
		} else {
			return false;
		}
	}

	//Redirect, to get rid of post
	public function redirect() {
		header('Location: ' . $_SERVER['PHP_SELF']);
	}

	//Page: login, page for logging in
	public function login() {
		$username = $this->cookieService->load('username'); //username || ''

		$body = "
				<h1>L2 - Login [kl222jy]</h1>
				<a href='?action=register'>Registrera en ny användare</a>
				<h2>Ej inloggad</h2>
				<form action='?' method='post'>
					<fieldset>
						<legend>Inloggning - Skriv in användarnamn och lösenord</legend>
						<label for='username'>Username</label>
						<input type='text' id='username' name='username' value='$username'>
						<label for='password'>Password</label>
						<input type='password' id='password' name='password'>
						<label for='remember'>Håll mig inloggad</label>
						<input type='checkbox' id='remember' name='remember'>
						<button type='submit' name='login' class='btn btn-primary'>Logga in</button>
					</fieldset>
				</form>";

		return $body;
	}

	//Page: logged in, page for logged in user
	public function loggedIn() {
		$username = $this->cookieService->load('username');
		if (!$username) {
			$username = $this->model->getUsername();
		}

		$body = "
			<h1>L2 - Login [kl222jy]</h1>
			<h2>$username är inloggad</h2>
			<a href='?action=logout'>Logga ut</a>";
		
		return $body;
	}
}