<?php

namespace App\AdminModule\Presenters;

use Nette,
	Nette\Utils\Strings,
	Nette\Security\Passwords;

class DefaultPresenter extends BasePresenter
{
	public function actionDefault()
	{
		//$this->template->hash   = Passwords::hash('heslo');
		$this->template->user   = $this->getUser()->getIdentity()->data;
		$this->template->email  = $this->getUser()->getIdentity()->data['email'];
	}
}
