<?php
namespace App\Model;

class UsersRepository extends BaseRepository
{
	/** @var Nette\Database\Context */
	protected $database;
	protected $table = 'users';
}