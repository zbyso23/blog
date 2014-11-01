<?php
namespace App\Model;

use Nette;
abstract class BaseRepository extends Nette\Object
{
	/** @var Nette\Database\Context */
	protected $database;
	protected $table;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	/** @return Nette\Database\Table\Selection */
	public function findAll()
	{
		return $this->database->table($this->table);
	}


	/** @return Nette\Database\Table\ActiveRow */
	public function findById($id)
	{
		return $this->findAll()->get($id);
	}


	/** @return Nette\Database\Table\ActiveRow */
	public function insert($values)
	{
		return $this->findAll()->insert($values);
	}
}