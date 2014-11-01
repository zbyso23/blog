<?php

namespace App\FrontModule\Presenters;

use App\Model;

class BasePresenter extends \App\Presenters\BasePresenter
{
	const MODULE = 'Front';

	public function stratup()
	{
		parent::startup();
	}
}