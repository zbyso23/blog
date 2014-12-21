<?php
namespace App\Model;

use Nette;
abstract class BaseRepository extends Nette\Object
{
	/** @var MongoClient */
	protected $connection;
	protected $database;
	protected $collection;

	const DATABASE = 'blog';

	public function __construct()
	{
		$this->connection = new \MongoClient();
		$database = self::DATABASE;
		$this->database = $this->connection->$database;
		$this->setCollection();
	}

	protected function setCollection()
	{
	}

	public function find($query = array())
	{
		return $this->collection->findOne($query);
	}

	/** @return Nette\Database\Table\Selection */
	public function findAll($query = array())
	{
		return $this->collection->find($query);
	}


	/** @return Nette\Database\Table\ActiveRow */
	public function findById($id)
	{
		return $this->findAll(array('id' => $id));
	}


	/** @return Nette\Database\Table\ActiveRow */
	public function insert($values)
	{
		return $this->collection->insert($values);
	}

	/** @return Nette\Database\Table\ActiveRow */
	public function update($query, $values)
	{
		$values = array('$set' => $values);
		return $this->collection->update($query, $values);
	}
}