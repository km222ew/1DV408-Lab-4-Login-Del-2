<?php

require_once("account.repository.php");

class AccountUserRepository extends AccountRepository
{
    private $db;
    private static $username = "username";
    private static $password = "password";
    private static $token = "token";
    private static $expire = "expire";

    public function __construct()
    {
        $this->dbTable = "user";
        $this->db = $this->connection();
    }

    //Insert new user into database
    public function addUser(AccountUser $user)
    {
        try
        {
            $sql = "INSERT INTO $this->dbTable (". self::$username . ", " . self::$password . ") VALUES (?, ?)";
            $params = array($user->getUsername(), $user->getPassword());

            $query = $this->db->prepare($sql);
            $query->execute($params);
        }
        catch(PDOException $e)
        {
            die("An error has occurred. Error code 222");
        }
    }

    public function getUserByName($username)
    {
        try
        {
            $sql = "SELECT * FROM $this->dbTable WHERE " . self::$username . "= ?";
            $params = array($username);

            $query = $this->db->prepare($sql);
            $query->execute($params);

            $result = $query->fetch();

            if($result)
            {
                $user = new AccountUser($result[self::$username],
                                        $result[self::$password],
                                        $result[self::$token],
                                        $result[self::$expire]);

                return $user;
            }

            return null;
        }
        catch(PDOException $e)
        {
            die("An error has occurred. Error code 123");
        }
    }

    //Updates the stored token and cookie expire time in database
    public function updateUserIdentifier($token, $expire, $username)
    {
        try
        {
            $sql = "UPDATE $this->dbTable SET ". self::$token . "= ?, " . self::$expire . "= ? WHERE " . self::$username . "= ?";
            $params = array($token, $expire, $username);

            $query = $this->db->prepare($sql);
            $query->execute($params);
        }
        catch(PDOException $e)
        {
            die("An error has occurred. Error code 355");
        }
    }
}