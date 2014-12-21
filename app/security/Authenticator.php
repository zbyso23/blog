<?php

namespace App\Security;

use Nette,
	Nette\Utils\Strings,
	Nette\Security\Passwords,
	App\Model;


/**
 * Users management.
 */
class Authenticator extends Nette\Object implements Nette\Security\IAuthenticator
{
	const
		ID            = 'id',
		EMAIL         = 'email',
		PASSWORD_HASH = 'password',
		ROLE          = 'role',
		ROLES_TABLE_NAME     = 'roles',
		ROLES_COLUMN_ID      = 'role_id';


	/** @var App\Model\UsersRepository */
	private $users;

	const SALT = 'DF161f1s313ef31r313fe1';

	public function __construct(Model\UsersRepository $users)
	{
		$this->users = $users;
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
		$query = array(self::EMAIL => $email);
		$row = $this->users->find($query);
		if (NULL === $row) {
			throw new Nette\Security\AuthenticationException('The email is incorrect.', self::IDENTITY_NOT_FOUND);

		} elseif (!Passwords::verify($password, $row[self::PASSWORD_HASH])) {
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);

		} elseif (Passwords::needsRehash($row[self::PASSWORD_HASH])) {
			$this->users->update(array(self::EMAIL => $email), array(
				self::PASSWORD_HASH => Passwords::hash($password),
			));
		}

		$userData = $row;
		unset($userData[self::PASSWORD_HASH]);
		//Not needed $rowRole = $this->database->table(self::ROLES_TABLE_NAME)->where(self::ROLES_COLUMN_ID, $row[self::ROLES_COLUMN_ID])->fetch();
		//return new Nette\Security\Identity($row[self::COLUMN_ID], $rowRole->name, $arr);
		return new Nette\Security\Identity($row[self::ID], $row[self::ROLE], $userData);
	}


	/**
	 * Adds new user.
	 * @param  string
	 * @param  string
	 * @return void
	 */
	public function add($email, $password)
	{
		$this->users->insert(array(
			self::EMAIL => $email,
			self::PASSWORD_HASH => Passwords::hash($password),
		));
	}
}