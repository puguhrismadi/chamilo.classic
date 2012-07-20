<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @license see /license.txt
 * @author autogenerated
 */
class AccessUrlRelUser extends \Entity
{
    /**
     * @return \Entity\Repository\AccessUrlRelUserRepository
     */
     public static function repository(){
        return \Entity\Repository\AccessUrlRelUserRepository::instance();
    }

    /**
     * @return \Entity\AccessUrlRelUser
     */
     public static function create(){
        return new self();
    }

    /**
     * @var integer $access_url_id
     */
    protected $access_url_id;

    /**
     * @var integer $user_id
     */
    protected $user_id;


    /**
     * Set access_url_id
     *
     * @param integer $value
     * @return AccessUrlRelUser
     */
    public function set_access_url_id($value)
    {
        $this->access_url_id = $value;
        return $this;
    }

    /**
     * Get access_url_id
     *
     * @return integer 
     */
    public function get_access_url_id()
    {
        return $this->access_url_id;
    }

    /**
     * Set user_id
     *
     * @param integer $value
     * @return AccessUrlRelUser
     */
    public function set_user_id($value)
    {
        $this->user_id = $value;
        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function get_user_id()
    {
        return $this->user_id;
    }
}