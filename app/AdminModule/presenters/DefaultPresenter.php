<?php

namespace App\AdminModule\Presenters;

use Nette,
	Nette\Utils\Strings,
	Nette\Security\Passwords;

class DefaultPresenter extends BasePresenter
{
	public function actionDefault()
	{
		$this->template->roles  = $this->roles->findAll()->order('role_id');
		$this->template->users  = $this->users->findAll()->order('user_id');
		$this->template->access = $this->access->findAll()->order('access_id');

		$this->template->hash   = Passwords::hash('heslo');
		$this->template->email  = $this->getUser()->getIdentity()->data['email'];
		// die('#');
	}
}
