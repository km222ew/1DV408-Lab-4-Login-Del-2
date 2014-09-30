<?php


class AccountController {

	private $model;
	private $view;
	private $username;
	private $password;

	public function __construct(AccountModel $model,AccountView $view) {
		$this->model = $model;
		$this->view = $view;
	}

	//validate login
	private function validateLogin() {
		$username = $this->view->getUsername();
		$password = $this->view->getPassword();
		$remember = $this->view->getRemember();

		//Check if credentials are correct
		if ($this->model->validateCredentials($username, $password, $remember, $this->view->getUserAgent())) {
			//Should we remember user?
			if ($remember) {
				$this->view->remember();
			}

			return true;
		} else {
			return false;
		}
	}

	public function index() {
		//Did user click logout?
		if ($this->view->didLogout()) {
			//Log user out
			$this->model->logOut();
		}

		//Did user click login?
		if ($this->view->didLogin()) {
			//Get rid of post request
			$this->view->redirect();

			//Validate credentials (post)
			if ($this->validateLogin()) {
				//Show logged in page
				return $this->view->loggedIn();
			}
		}

		//Is user already logged in? (session)
		if ($this->model->isLoggedIn($this->view->getUserAgent())) {
			return $this->view->loggedIn();
		}

		//Check for token (cookie)
		if ($this->view->getToken() != '') {
			$token = $this->view->getToken();
			
			//Check if token is correct
			if ($this->model->validateToken($token, $this->view->getUserAgent())) {
				return $this->view->loggedIn();
			} else {
				$this->view->removeToken();
			}
		}

		return $this->view->login();
	}
}