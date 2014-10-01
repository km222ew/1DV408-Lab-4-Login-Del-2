<?php

class AccountRegisterModel
{
    private $notify;
    private $filteredUsername;

    public function __construct(Notify $notify) {
        //Notifications notify->success/error/info(message, optional header)
        $this->notify = $notify;

    }


    public function validateRegister($username, $password, $repPassword)
    {
        $validChars = '/[^a-zåäöA-ZÅÄÖ0-9]/';

        if($password != $repPassword)
        {
            $this->notify->error('Lösenorden matchar inte');
        }

        if(strlen($username) < 3)
        {
            $this->notify->error('Användarnamnet har för få tecken. Minst 3 tecken');
        }

        //Check if username contains non alphanumeric characters
        if(preg_match($validChars, $username))
        {
            $this->notify->error('Användarnamnet innehåller ogiltiga tecken');
        }

        if(strlen($password) < 6)
        {
            $this->notify->error('Lösenorden har för få tecken. Minst 6 tecken');
        }


        //Check if username exist in database

        //Input user into database

        $this->notify->success('Registrering av ny användare lyckades');
        return true;
    }
}