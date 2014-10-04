<?php

class AccountView {

	private $model;
	public $cookieService;

    public function __construct(AccountModel $model, CookieService $cookieService)
    {
        $this->model = $model;
        $this->cookieService = $cookieService;
    }

	//Get username from post
	public function getUsername()
    {
		if (isset($_POST['username']))
        {
			//Save username in cookie to remember input
			$this->cookieService->save('inputUsername', $_POST['username'], time()+60);
			return trim($_POST['username']);
		}
		
		return '';
	}

	//Get password from post
	public function getPassword()
    {
		if (isset($_POST['password']) && $_POST['password'] != '')
        {
			return trim($_POST['password']);
			//return $password = crypt($_POST['password'], $this->getUsername());
		}

		return '';
	}

	//Remember user?
	public function getRemember()
    {
		if (isset($_POST['remember']))
        {
			//return $_POST['remember'];
            return true;
		}
        else
        {
			return false;
		}
	}

    //Get username stored in cookie
    public function getUsernameCookie()
    {
        return $_COOKIE['username'];
    }

    //Get token stored in cookie
    public function getTokenPassCookie()
    {
        return $_COOKIE['tokenpass'];
    }

	//Get client browser info
	public function getUserAgent()
    {
		return $_SERVER['HTTP_USER_AGENT'];
	}

    //Get client ip
    public function getUserIp()
    {
        return $_SERVER["REMOTE_ADDR"];
    }

	//Did user request login?
	public function didLogin()
    {
		if (isset($_POST['login']))
        {
			return true;
		}
        else
        {
			return false;
		}
	}

	//Did user request to be logged out?
	public function didLogout()
    {
		if (isset($_GET['action']) && $_GET['action'] == 'logout') {
			return true;
		}
        else
        {
			return false;
		}
	}

    public function goToRegister()
    {
        if(isset($_GET['action']) && $_GET['action'] == 'register')
        {
            return true;
        }
        else
        {
            return false;
        }
    }

	//Redirect, to get rid of post, changed to work with different pages
	public function redirect($pageId)
    {
        header("Location:$pageId");
	}

	//Page: login, page for logging in
	public function login()
    {

		$username = $this->cookieService->load('inputUsername');

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
	public function loggedIn()
    {
		$username = $this->cookieService->load('inputUsername');

		if (!$username)
        {
			$username = $this->model->getUsername();
		}

		$body = "
			<h1>L2 - Login [kl222jy]</h1>
			<h2>$username är inloggad</h2>
			<a href='?action=logout'>Logga ut</a>";
		
		return $body;
	}

    public function createCookies($username, $tokenPass)
    {
        $time = time()+60;

        setcookie("username", $username, $time);
        setcookie("tokenpass", $tokenPass, $time);

        return $time;
    }

    public function deleteCookies()
    {
        setcookie("username", "", time()-1);
        setcookie("tokenpass","", time()-1);
    }

    public function cookiesExist()
    {
        if(isset($_COOKIE['username']) === true && isset($_COOKIE["tokenpass"]) === true)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}