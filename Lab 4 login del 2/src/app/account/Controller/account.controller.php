<?php


class AccountController {

	private $model;
    private $registerModel;
	private $view;
    private $registerView;

	public function __construct(AccountModel $model,AccountRegisterModel $registerModel,
                                AccountView $view, AccountRegisterView $registerView)
    {
		$this->model = $model;
        $this->registerModel = $registerModel;
		$this->view = $view;
        $this->registerView = $registerView;
	}

    public function refreshCookies($username)
    {
        $expiration = $this->view->createCookies($username, $this->model->getToken());
        $this->model->rememberUser($expiration, $username);
    }

	//validate login
	private function validateLogin()
    {
		$username = $this->view->getUsername();
		$password = $this->view->getPassword();
        $remember = $this->view->getRemember();
        $userAgent = $this->view->getUserAgent();
        $userIp = $this->view->getUserIp();

		//Check if credentials are correct
		if ($this->model->validateCredentials($username, $password, $userAgent, $userIp, $remember))
        {
            if($remember)
            {
                $expiration = $this->view->createCookies($username, $this->model->getToken());
                $this->model->rememberUser($expiration, $username);
            }

			return true;
		}
        else
        {
			return false;
		}
	}

	public function index()
    {

        //Is user already logged in? (session)
        if ($this->model->isLoggedIn($this->view->getUserAgent(), $this->view->getUserIp()))
        {
            //Did user click logout?
            if ($this->view->didLogout())
            {

                //Log user out
                $this->model->logOut();

                if($this->view->cookiesExist())
                {
                    $this->view->deleteCookies();
                }

                return $this->view->login();
            }
            else
            {
                return $this->view->loggedIn();
            }
        }
        else
        {
            if($this->view->cookiesExist())
            {
                if($this->model->loginWithCookies($this->view->getUsernameCookie(), $this->view->getTokenPassCookie(),
                    $this->view->getUserIP(), $this->view->getUserAgent()))
                {
                    //view logged in with success cookie message
                    $this->refreshCookies($this->view->getUsernameCookie());
                    return $this->view->loggedIn();
                }
                else
                {
                    $this->view->deleteCookies();
                    return $this->view->login();
                }
            }

            //Did user click login?
            if ($this->view->didLogin())
            {
                //Get rid of post request
                $this->view->redirect("index.php");

                //Validate credentials (post)
                if ($this->validateLogin())
                {
                    //Show logged in page

                    return $this->view->loggedIn();
                }
            }

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
        }

		return $this->view->login();
	}
}