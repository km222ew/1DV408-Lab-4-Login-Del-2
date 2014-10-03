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

    public function addUser(AccountUser $user)
    {

            $sql = "INSERT INTO $this->dbTable (". self::$username . ", " . self::$password . ") VALUES (?, ?)";
            $params = array($user->getUsername(), $user->getPassword());

            $query = $this->db->prepare($sql);
            $query->execute($params);

    }

    public function getUserByName($username)
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

    public function updateUserIdentifier($token, $expire, $username)
    {

            $sql = "UPDATE $this->dbTable SET ". self::$token . "= ?, " . self::$expire . "= ? WHERE " . self::$username . "= ?";
            $params = array($token, $expire, $username);

            $query = $this->db->prepare($sql);
            $query->execute($params);


    }
}