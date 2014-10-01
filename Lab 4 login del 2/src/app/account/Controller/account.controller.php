<?php


class AccountController {

	private $model;
    private $registerModel;
	private $view;
    private $registerView;
	private $username;
	private $password;

	public function __construct(AccountModel $model,AccountRegisterModel $registerModel, AccountView $view, AccountRegisterView $registerView) {
		$this->model = $model;
        $this->registerModel = $registerModel;
		$this->view = $view;
        $this->registerView = $registerView;
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

        //Did use press register
        if($this->registerView->didRegister())
        {
            $username = $this->registerView->getUsername();
            $password = $this->registerView->getPassword();
            $repPassword = $this->registerView->getRepeatedPassword();

            //Check if the inputs are valid
            if($this->registerModel->validateRegister($username, $password, $repPassword))
            {
                //Get rid of post request
                $this->view->redirect("index.php");
                return $this->view->login();
            }

            //Get rid of post request
            $this->view->redirect("?action=register");
            return $this->registerView->register();
        }

        //Did user go to register page
        if($this->view->goToRegister())
        {
            return $this->registerView->register();
        }

		//Did user click logout?
		if ($this->view->didLogout()) {
			//Log user out
			$this->model->logOut();
		}

		//Did user click login?
		if ($this->view->didLogin()) {
			//Get rid of post request
			$this->view->redirect("index.php");

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