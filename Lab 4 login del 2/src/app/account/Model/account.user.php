<?php

class AccountUser
{
    private $username;
    private $password;
    private $token;
    private $expire;

    public function __construct($username, $password, $token, $expire)
    {
        $this->username = $username;
        $this->password = $password;
        $this->token = $token;
        $this->expire = $expire;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getExpire()
    {
        return $this->expire;
    }
}