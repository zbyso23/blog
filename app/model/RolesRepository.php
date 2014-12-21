<?php
namespace App\Model;

class RolesRepository extends BaseRepository
{
	const COLLECTION = 'roles';

	protected function setCollection()
	{
		$collection = self::COLLECTION;
		$this->collection = $this->database->$collection;
	}
}