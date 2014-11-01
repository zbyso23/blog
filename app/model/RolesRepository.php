<?php
namespace App\Model;

class RolesRepository extends BaseRepository
{
	/** @var Nette\Database\Context */
	protected $database;
	protected $table = 'roles';
}