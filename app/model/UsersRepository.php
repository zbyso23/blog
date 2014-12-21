<?php
namespace App\Model;

class UsersRepository extends BaseRepository
{
	const COLLECTION = 'users';

	protected function setCollection()
	{
		$collection = self::COLLECTION;
		$this->collection = $this->database->$collection;
	}
}