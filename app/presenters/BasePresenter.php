<?php

namespace Presenters;

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

    protected $filesJs    = array('loader'   => array(), 'header'  => array());
    protected $filesCss   = array('loader'   => array(), 'header'  => array());

    protected $module;

	public function __construct(Model\UsersRepository $users, Model\RolesRepository $roles, Model\AccessRepository $access)
	{
		$this->roles  = $roles;
		$this->access = $access;
		$this->users  = $users;
	}

	public function startup()
	{
		parent::startup();
		$this->user = $this->getUser();
		$this->isLoggedIn = $this->user->isLoggedIn();
		if(true === $this->isLoggedIn) $this->user->getAuthorizator()->initialize($this->user->getIdentity());
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

        $this->template->filesJs  = $this->filesJs['header'];
        $this->template->filesCss = $this->filesCss['header'];
        $this->template->language = 'cs';
	}


    protected function hasPermission($action = FALSE)
    {
        $action = (!$action) ? $this->getAction() : $action;
        //Group actions to operations - like action enable to update
        if(array_key_exists($action, $this->permissionGroups)) $action = $this->permissionGroups[$action];
        // check if user has permission
		$resource = $this->getResource();
        if(($resource === $this->module.':Users' && ($action === 'login' || $action === 'logout')) || 
            $resource === $this->module.':Default' && ($action === 'default' || $action === 'error'))
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

    public function getResource($short = false)
	{
        if(!preg_match('/^[a-zA-Z0-9]+\:([a-zA-Z0-9]+)$/', $this->getName(), $matches))
        {
            return $this->module.':'.$this->getName();
        }
		return (false === $short) ? $this->module.':'.$matches[1] : $matches[1];
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

    protected function addFilesJs($files, $loader = true)
    {
        $key = ($loader) ? 'loader' : 'header';
        foreach($files as $file)
        {
            if(in_array($file, $this->filesJs[$key])) continue;
            $this->filesJs[$key][] = $file;    
        }
    }
    
    protected function addFilesCss($files, $loader = true)
    {
        $key = ($loader) ? 'loader' : 'header';
        foreach($files as $file)
        {
            if(in_array($file, $this->filesCss[$key])) continue;
            $this->filesCss[$key][] = $file;
        }
    }
    
    protected function createComponentJs()
    {
        $files = new \WebLoader\FileCollection($this->context->parameters['wwwDir'].'/js');
        $files->addFiles($this->filesJs['loader']);
        $compiler = \WebLoader\Compiler::createJsCompiler($files, $this->context->parameters['wwwDir'] . '/webtemp');
        return new \WebLoader\Nette\JavaScriptLoader($compiler, $this->template->basePath . '/webtemp');
    }
    
    protected function createComponentCss()
    {
        $files = new \WebLoader\FileCollection($this->context->parameters['wwwDir'].'/css');
        $files->addFiles($this->filesCss['loader']);
        $compiler = \WebLoader\Compiler::createCssCompiler($files, $this->context->parameters['wwwDir'] . '/webtemp');
        return new \WebLoader\Nette\CssLoader($compiler, $this->template->basePath . '/webtemp');
    }
}