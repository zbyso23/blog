<?php
namespace App\AdminModule\Presenters;

abstract class BasePresenter extends \Presenters\BasePresenter
{
	protected $module = 'Admin';


	public function startup()
	{
		parent::startup();
        if(false === $this->isLoggedIn && false === ($this->getResource(true) === 'Sign' && $this->getAction() === 'login'))
        {
            $url = $this->link('Sign:login').'?redirect='.$this->getUrl();
            $this->redirectUrl($url);
            $this->terminate();
        }
        
	}
}