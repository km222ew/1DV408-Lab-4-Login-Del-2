<?php

//only used to be able to store and retrieve username in the input field
class CookieService {
	private static $cookieName = "CookieService";

	//Save cookie
	public function save($name, $string, $expiration) {
		setcookie( self::$cookieName . '::' . $name, $string, $expiration);
	}

	//Load cookie
	public function load($name) {
		if (!isset($_COOKIE[self::$cookieName . '::' . $name])) {
			return '';
		}

		$value = $_COOKIE[self::$cookieName . '::' . $name];

        $_COOKIE[self::$cookieName . '::' . $name] = '';
        setcookie(self::$cookieName . '::' . $name, '', time()-1);

		return $value;
	}
}