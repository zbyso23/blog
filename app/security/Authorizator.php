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

	/** @var Model\RolesRepository */
	protected $dbRoles;
    
	/** @var Model\AccessRepository */
	protected $dbAccess;
    
    private $userIdentity;
    private $isInitialized = false;
    
    private $userRoles = array();
    
    static $resources;
    
	//public function __construct(\DBUsers $users, \DBRoles $roles, \DBAccess $access)
    public function __construct(Model\UsersRepository $users, Model\RolesRepository $roles, Model\AccessRepository $access)
	{
		$this->dbUsers  = $users;
        $this->dbRoles  = $roles;
        $this->dbAccess = $access;
        //$this->loadResources();
	}

	private function loadResources() 
    {
        if(is_array(self::$resources)) return self::$resources;
        self::$resources = array();
        $access = $this->dbAccess->get();
        foreach($access as $resource)
        {
            if(!array_key_exists($resource->resource, self::$resources))
            {
                self::$resources[$resource->resource] = array();
            }
            self::$resources[$resource->resource][$resource->privilege] = false;
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
            return false;
        }
        return true;
	}

    public function initialize(Nette\Security\Identity $userIdentity)
    {
        $this->userIdentity = $userIdentity;
        $userRoles = $this->dbUsers->getRolesByEmail($this->userIdentity->email);

        $isSuperadmin = false;
		// add all roles
		foreach ($userRoles as $role)
        {
			$this->userRoles[$role->role_id] = $role->name;
            if($role->name === 'superadmin')
            {
                $isSuperadmin = true;
                break;
            }
		}
        
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
        
        $acl = $this->dbRoles->getACL();
        foreach($acl as $access)
        {
            if (array_key_exists($access->role_id, $this->userRoles))
            {
                if (array_key_exists($access->resource, self::$resources) &&
                    array_key_exists($access->privilege, self::$resources[$access->resource]))
                {
                    self::$resources[$access->resource][$access->privilege] = true;
                }
            }
        }



        $this->isInitialized = true;
    }
}