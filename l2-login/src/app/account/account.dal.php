<?php

//Manages remembered accounts
//txt file with token, username, expiration
class AccountDAL {
	//Filepath
	private $rememberedAccounts = './data/remembered.accounts.txt';

	//Constructor
	public function __construct() {

	}

	//Saves token to file
	public function rememberUser($token, $username, $expirationDate) {
		//Get rid of previous instances of token
		$this->removeByToken($token);
		//Open file
		$rememberedAccountsFile = fopen($this->rememberedAccounts, 'a');
		//Write "token, username, expiration"
		fwrite($rememberedAccountsFile, "$token,$username,$expirationDate\n");
		//Close file
		fclose($rememberedAccountsFile);
	}

	//Removes lines with token
	private function removeByToken($token) {
		//Get array of lines
		$rememberedAccounts = @file($this->rememberedAccounts);

		//Make sure file isn't empty
		if ($rememberedAccounts === false) {
			return;
		}

		foreach ($rememberedAccounts as $key => $rememberedAccount) {
			//Splits line into token, username, expiration
			$account = explode(',', $rememberedAccount);
			$accountToken = $account[0];

			//Token found?
			if ($accountToken == $token) {
				//..remove
				unset($rememberedAccounts[$key]);
			}
		}

		//Rebuild lines for file
		$data = implode('\n', array_values($rememberedAccounts));
		$rememberedAccountsFile = fopen($this->rememberedAccounts, 'w');
		//Update file
		fwrite($rememberedAccountsFile, $data);
		fclose($rememberedAccountsFile);
	}

	//Tries to find user by token
	public function findRememberedUser($token) {
		$rememberedAccounts = @file($this->rememberedAccounts);
		$now = time()+1;
		
		//Make sure file isn't empty
		if ($rememberedAccounts === false) {
			return false;
		}

		//Search lines for token
		foreach ($rememberedAccounts as $rememberedAccount) {
			$account = explode(',', $rememberedAccount);
			$accountToken = $account[0];
			$accountName = $account[1];
			$accountExpiration = $account[2];

			//Match?
			if ($accountToken == $token) {
				//Make sure it hasn't expired
				if ($now < $accountExpiration) {
					return $accountName;
				}

				return false;
			}
		}

		return false;
	}
}