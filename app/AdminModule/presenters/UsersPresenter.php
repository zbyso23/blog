<?php

namespace App\AdminModule\Presenters;

use Nette,
    App\Model,
	Nette\Utils\Strings,
	Nette\Security\Passwords;

class UsersPresenter extends BasePresenter
{
	/** @var Model\UsersRepository */
	protected $dbUsers;

	/** @var Model\RolesRepository */
	protected $dbRoles;
    
	/** @var Model\AccessRepository */
	protected $dbAccess;

    public function startup()
	{
		parent::startup();
		$this->dbUsers  = $this->getContext()->getService('users');
        $this->dbRoles  = $this->getContext()->getService('roles');
        $this->dbAccess = $this->getContext()->getService('access');
	}

	public function actionDefault()
	{
		$users     = $this->dbUsers->findAll();
		$this->template->users = $users;
	}

	public function actionActivate($id)
	{
		$query = array('id' => (int) $id);
		$this->dbUsers->update($query, array('active' => 1));
		$this->redirect('default');
	}

	public function actionDeactivate($id)
	{
		$query = array('id' => (int) $id);
		$result = $this->dbUsers->update($query, array('active' => 0));
		//var_export($result);die('X');
		$this->redirect('default');
	}

}