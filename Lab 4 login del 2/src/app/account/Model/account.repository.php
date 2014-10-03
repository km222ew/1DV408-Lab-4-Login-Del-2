<?php

class AccountRepository
{
    protected $dbConnection;
    protected $dbTable;
    protected $dbUsername = 'root';
    protected $dbPassword = '';
    protected $dbConnstring = 'mysql:host=127.0.0.1;dbname=userdb';

    public function connection()
    {
        if ($this->dbConnection == NULL)
        {
            $this->dbConnection = new \PDO($this->dbConnstring, $this->dbUsername, $this->dbPassword);
        }

        $this->dbConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $this->dbConnection;
    }
}