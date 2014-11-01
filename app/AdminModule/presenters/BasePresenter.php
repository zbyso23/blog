<?php
namespace App\AdminModule\Presenters;

abstract class BasePresenter extends \App\Presenters\BasePresenter
{
	const MODULE = 'Admin';

	public function startup()
	{
		parent::startup();
        if(false === $this->isLoggedIn && false === ($this->getResource() === 'Sign' && $this->getAction() === 'login'))
        {
            $url = $this->link('Sign:login').'?redirect='.$this->getUrl();
            $this->redirectUrl($url);
            $this->terminate();
        }
	}
}