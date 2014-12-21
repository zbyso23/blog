<?php

namespace App\Security;

use Nette,
	Nette\Utils\Strings,
	Nette\Security\Passwords;


/**
 * Users management.
 */
class Authenticator extends Nette\Object implements Nette\Security\IAuthenticator
{
	const
		TABLE_NAME           = 'users',
		COLUMN_ID            = 'user_id',
		COLUMN_EMAIL         = 'email',
		COLUMN_PASSWORD_HASH = 'password',
		COLUMN_ROLE          = 'role',
		ROLES_TABLE_NAME     = 'roles',
		ROLES_COLUMN_ID      = 'role_id';


	/** @var Nette\Database\Context */
	private $database;

	const SALT = 'DF161f1s313ef31r313fe1';

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	public function hash($password)
	{
		return Passwords::hash($password);
	}

	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($email, $password) = $credentials;
		$row = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_EMAIL, $email)->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('The email is incorrect.', self::IDENTITY_NOT_FOUND);

		} elseif (!Passwords::verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);

		} elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
			$row->update(array(
				self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
			));
		}

		$arr = $row->toArray();
		unset($arr[self::COLUMN_PASSWORD_HASH]);
		//Not needed $rowRole = $this->database->table(self::ROLES_TABLE_NAME)->where(self::ROLES_COLUMN_ID, $row[self::ROLES_COLUMN_ID])->fetch();
		//return new Nette\Security\Identity($row[self::COLUMN_ID], $rowRole->name, $arr);
		return new Nette\Security\Identity($row[self::COLUMN_ID], $row[self::ROLES_COLUMN_ID], $arr);
	}


	/**
	 * Adds new user.
	 * @param  string
	 * @param  string
	 * @return void
	 */
	public function add($email, $password)
	{
		$this->database->table(self::TABLE_NAME)->insert(array(
			self::COLUMN_EMAIL => $email,
			self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
		));
	}
}
