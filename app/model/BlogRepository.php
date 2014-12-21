<?php
namespace App\Model;

class BlogRepository extends BaseRepository
{
	const COLLECTION = 'blog';

	protected function setCollection()
	{
		$collection = self::COLLECTION;
		$this->collection = $this->database->$collection;
	}
}