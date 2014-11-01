<?php

namespace App\Presenters;

use Nette,
	App\Model;


abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	/** @var Model\UsersRepository */
	protected $users;
	/** @var Model\RolesRepository */
	protected $roles;
	/** @var Model\AccessRepository */
	protected $access;

	protected $user;
	protected $isLoggedIn;

	public function __construct(Model\UsersRepository $users, Model\RolesRepository $roles, Model\AccessRepository $access)
	{
		$this->roles  = $roles;
		$this->access = $access;
		$this->users  = $users;
	}

	public function stratup()
	{
		parent::startup();
		$this->user = $this->getUser();
		$this->isLoggedIn = $this->user->isLoggedIn();
		$this->user->getAuthorizator()->initialize($this->user->getIdentity());
	}

	protected function beforeRender()
	{
		$this->template->viewName = $this->view;
		$this->template->root = isset($_SERVER['SCRIPT_FILENAME']) ? realpath(dirname(dirname($_SERVER['SCRIPT_FILENAME']))) : NULL;

		$a = strrpos($this->name, ':');
		if ($a === FALSE) {
			$this->template->moduleName = '';
			$this->template->presenterName = $this->name;
		} else {
			$this->template->moduleName = substr($this->name, 0, $a + 1);
			$this->template->presenterName = substr($this->name, $a + 1);
		}
	}


    protected function hasPermission($action = FALSE)
    {
        $action = (!$action) ? $this->getAction() : $action;
        //Group actions to operations - like action enable to update
        if(array_key_exists($action, $this->permissionGroups)) $action = $this->permissionGroups[$action];
        // check if user has permission
		$resource = $this->getResource();
        if(($resource === self::MODULE.':Users' && ($action === 'login' || $action === 'logout')) || 
            $resource === self::MODULE.':Default' && ($action === 'default' || $action === 'error'))
        {
            return true;
        }
		$access = false;
		if ($this->getUser()->getAuthorizator()->hasResource($resource, $action))
        {
            if ($this->getUser()->getAuthorizator()->hasAccess($resource, $action) === true)
            {
                $access = true;
            }
		}
        return $access;
    }

    protected function getAccess($actions)
    {
        $access = array();
        if(!is_array($actions)) $actions = array($actions);
        foreach($actions as $action)
        {
            $access[$action] = $this->hasPermission($action);
        }
        return $access;
    }

    public function getResource()
	{
        if(!preg_match('/^[a-zA-Z0-9]+\:([a-zA-Z0-9]+)$/', $this->getName(), $matches))
        {
            return self::MODULE.':'.$this->getName();
        }
		return self::MODULE.':'.$matches[1];
	}

    protected function isUserInRole($roleName)
    {
        foreach($this->user->getIdentity()->roles as $roleRowName)
        {
            if($roleRowName === $roleName) return true;
        }
        return false;
    }

    protected function getUrl()
    {
        $httpRequest = $this->context->getService('httpRequest');
        $uri = $httpRequest->getUrl();
        return 'http://'.$uri->host;
    }
}