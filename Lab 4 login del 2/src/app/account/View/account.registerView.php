<?php

class AccountRegisterView
{
    private $cookieService;

    public function __construct(CookieService $cookieService)
    {
        $this->cookieService = $cookieService;
    }

    public function getUsername()
    {
        if(isset($_POST['regUsername']))
        {
            return trim($_POST['regUsername']);
        }

        return '';
    }

    public function getPassword()
    {
        if(isset($_POST['regPassword']))
        {
            return trim($_POST['regPassword']);
        }

        return '';
    }

    public function getRepeatedPassword()
    {
        if(isset($_POST['repRegPassword']))
        {
            return trim($_POST['repRegPassword']);
        }

        return '';
    }

    public function didRegister()
    {
        if(isset($_POST['register']))
        {
            $this->cookieService->save('inputUsername', $this->getUsername(), time()+60);
            return true;
        }
        else
        {
            return false;
        }
    }

    public function register() {

        $username = $this->cookieService->load('inputUsername');

        //Replaces all invalid characters with '' and puts the remainder in input
        $username = preg_replace('/[^a-zåäöA-ZÅÄÖ0-9]/', '', $username);

        $body = "
				<h1>L2 - Login [kl222jy]</h1>
				<a href='index.php'>Tillbaka</a>
				<h2>Ej inloggad, Registrera användare</h2>
				<form action='?action=register' method='post'>
					<fieldset>
						<legend>Registrera ny användare - Skriv in användarnamn och lösenord</legend>
						<label for='regUsername'>Username</label>
						<input type='text' id='regUsername' name='regUsername' value='$username'>
						<label for='regPassword'>Password</label>
						<input type='password' id='regPassword' name='regPassword'>
						<label for='repRegPassword'>Repeat password</label>
						<input type='password' id='repRegPassword' name='repRegPassword'>
						<button type='submit' name='register' class='btn btn-primary'>Registrera</button>
					</fieldset>
				</form>";

        return $body;
    }
}