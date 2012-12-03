<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @license see /license.txt
 * @author autogenerated
 */
class Thematic extends \CourseEntity
{
    /**
     * @return \Entity\Repository\ThematicRepository
     */
     public static function repository(){
        return \Entity\Repository\ThematicRepository::instance();
    }

    /**
     * @return \Entity\Thematic
     */
     public static function create(){
        return new self();
    }

    /**
     * @var integer $c_id
     */
    protected $c_id;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var string $title
     */
    protected $title;

    /**
     * @var text $content
     */
    protected $content;

    /**
     * @var integer $display_order
     */
    protected $display_order;

    /**
     * @var boolean $active
     */
    protected $active;

    /**
     * @var integer $session_id
     */
    protected $session_id;


    /**
     * Set c_id
     *
     * @param integer $value
     * @return Thematic
     */
    public function set_c_id($value)
    {
        $this->c_id = $value;
        return $this;
    }

    /**
     * Get c_id
     *
     * @return integer 
     */
    public function get_c_id()
    {
        return $this->c_id;
    }

    /**
     * Set id
     *
     * @param integer $value
     * @return Thematic
     */
    public function set_id($value)
    {
        $this->id = $value;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $value
     * @return Thematic
     */
    public function set_title($value)
    {
        $this->title = $value;
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param text $value
     * @return Thematic
     */
    public function set_content($value)
    {
        $this->content = $value;
        return $this;
    }

    /**
     * Get content
     *
     * @return text 
     */
    public function get_content()
    {
        return $this->content;
    }

    /**
     * Set display_order
     *
     * @param integer $value
     * @return Thematic
     */
    public function set_display_order($value)
    {
        $this->display_order = $value;
        return $this;
    }

    /**
     * Get display_order
     *
     * @return integer 
     */
    public function get_display_order()
    {
        return $this->display_order;
    }

    /**
     * Set active
     *
     * @param boolean $value
     * @return Thematic
     */
    public function set_active($value)
    {
        $this->active = $value;
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function get_active()
    {
        return $this->active;
    }

    /**
     * Set session_id
     *
     * @param integer $value
     * @return Thematic
     */
    public function set_session_id($value)
    {
        $this->session_id = $value;
        return $this;
    }

    /**
     * Get session_id
     *
     * @return integer 
     */
    public function get_session_id()
    {
        return $this->session_id;
    }
}