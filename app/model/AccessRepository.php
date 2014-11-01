<?php
namespace App\Model;

class AccessRepository extends BaseRepository
{
	/** @var Nette\Database\Context */
	protected $database;
	protected $table = 'access';
}