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
		$usersList = array();
		foreach ($users as $user)
		{
			$roleId      = $user->role_id;
			$role        = $this->dbRoles->findAll()->where('role_id = ?', $user->role_id)->fetch();
			$usersList[] = array('detail' => $user, 'role' => $role);
		}
		$this->template->users = $usersList;
	}

	public function actionActivate($id)
	{
		$this->dbUsers->update(array('active' => 1))->where('user_id = ?', $id);
		$this->redirect('default');
	}

	public function actionDeactivate($id)
	{
		$this->dbUsers->update(array('active' => 0))->where('user_id = ?', $id);
		$this->redirect('default');
	}

}