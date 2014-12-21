<?php
namespace App\Model;

class AccessRepository extends BaseRepository
{
	const COLLECTION = 'access';

	protected function setCollection()
	{
		$collection = self::COLLECTION;
		$this->collection = $this->database->$collection;
	}
}