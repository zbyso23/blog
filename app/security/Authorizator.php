<?php

namespace App\Security;

use Nette,
    App\Model;
use	Nette\Security as NS;

/**
 * Authorizator fills the ACL tables
 */
class Authorizator extends NS\Permission
{

	/** @var Model\UsersRepository */
	protected $dbUsers;

	/** @var Model\AccessRepository */
	protected $dbAccess;
    
    private $userIdentity;
    private $isInitialized = false;
    
    private $userRoles = array();
    
    static $resources;
    
    public function __construct(Model\UsersRepository $users, Model\AccessRepository $access)
	{
		$this->dbUsers  = $users;
        $this->dbAccess = $access;
        $this->loadResources();
	}

	private function loadResources() 
    {
        if(is_array(self::$resources)) return self::$resources;
        self::$resources = array();
        $access = $this->dbAccess->findAll();
        foreach($access as $resource)
        {
            if(!array_key_exists($resource['resource'], self::$resources))
            {
                self::$resources[$resource['resource']] = array();
            }
            self::$resources[$resource['resource']][$resource['action']] = false;
        }
        return self::$resources;
	}

	/**
	 * Returns NULL if the Resource not exists in the list. Otherwise return access to resource.
	 * @param $resource	string
	 * @param $privilege string
	 * @return bool
	 */
    public function hasAccessResource($resource)
    {
		return (array_key_exists($resource, self::$resources)) ? self::$resources[$resource] : array();
    }

    
	/**
	 * Returns NULL if the Resource not exists in the list. Otherwise return access to resource.
	 * @param $resource	string
	 * @param $privilege string
	 * @return bool
	 */
    public function hasAccess($resource, $privilege)
    {
		if (!array_key_exists($resource, self::$resources) ||
            !array_key_exists($privilege, self::$resources[$resource]))
        {
            return NULL;
        }
        return self::$resources[$resource][$privilege];
    }
    
	/**
	 * Returns TRUE if the Resource exists in the list.
	 * @param $resource	string
	 * @param $privilege string
	 * @return bool
	 */
	public function hasResource($resource, $privilege = NULL)
	{
		if (!array_key_exists($resource, self::$resources) ||
            !array_key_exists($privilege, self::$resources[$resource]))
        {
            $this->dbAccess->insert(array('resource' => $resource, 'action' => $privilege, 'role' => array('superadmin')));
        }
        return true;
	}

    public function initialize(Nette\Security\Identity $userIdentity)
    {
        $this->userIdentity = $userIdentity;
        $role = $this->userIdentity->roles[0];
        $isSuperadmin = ($role === 'superadmin');
        if($isSuperadmin)
        {
            foreach(self::$resources as $resource => $privileges)
            {
                foreach($privileges as $privilege => $access)
                {
                    self::$resources[$resource][$privilege] = true;
                }
            }
            $this->isInitialized = true;
            return;
        }
        
        $access = $this->dbAccess->findAll();
        foreach($access as $resource)
        {
            if (array_key_exists($role, $resource['role'])) self::$resources[$access->resource][$access->privilege] = true;
        }
        $this->isInitialized = true;
    }
}