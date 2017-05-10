<?php
namespace UserBundle\Services;
/**
 * Created by PhpStorm.
 * User: Fernando
 * Date: 14/04/2017
 * Time: 21:39
 */
class RolesHelper
{
    private $rolesHierarchy;

    public function __construct($rolesHierarchy)
    {
        $this->rolesHierarchy = $rolesHierarchy;
    }

    /**
     * Return roles.
     *
     * @return array
     */
    public function getRoles()
    {
        $roles = array();

        array_walk_recursive($this->rolesHierarchy, function($val) use (&$roles) {
            $roles[$val] = $val;
        });

        return array_unique($roles);
    }
}