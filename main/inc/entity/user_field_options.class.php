<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @license see /license.txt
 * @author autogenerated
 */
class UserFieldOptions extends \Entity
{
    /**
     * @return \Entity\Repository\UserFieldOptionsRepository
     */
     public static function repository(){
        return \Entity\Repository\UserFieldOptionsRepository::instance();
    }

    /**
     * @return \Entity\UserFieldOptions
     */
     public static function create(){
        return new self();
    }

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var integer $field_id
     */
    protected $field_id;

    /**
     * @var text $option_value
     */
    protected $option_value;

    /**
     * @var string $option_display_text
     */
    protected $option_display_text;

    /**
     * @var integer $option_order
     */
    protected $option_order;

    /**
     * @var datetime $tms
     */
    protected $tms;


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
     * Set field_id
     *
     * @param integer $value
     * @return UserFieldOptions
     */
    public function set_field_id($value)
    {
        $this->field_id = $value;
        return $this;
    }

    /**
     * Get field_id
     *
     * @return integer 
     */
    public function get_field_id()
    {
        return $this->field_id;
    }

    /**
     * Set option_value
     *
     * @param text $value
     * @return UserFieldOptions
     */
    public function set_option_value($value)
    {
        $this->option_value = $value;
        return $this;
    }

    /**
     * Get option_value
     *
     * @return text 
     */
    public function get_option_value()
    {
        return $this->option_value;
    }

    /**
     * Set option_display_text
     *
     * @param string $value
     * @return UserFieldOptions
     */
    public function set_option_display_text($value)
    {
        $this->option_display_text = $value;
        return $this;
    }

    /**
     * Get option_display_text
     *
     * @return string 
     */
    public function get_option_display_text()
    {
        return $this->option_display_text;
    }

    /**
     * Set option_order
     *
     * @param integer $value
     * @return UserFieldOptions
     */
    public function set_option_order($value)
    {
        $this->option_order = $value;
        return $this;
    }

    /**
     * Get option_order
     *
     * @return integer 
     */
    public function get_option_order()
    {
        return $this->option_order;
    }

    /**
     * Set tms
     *
     * @param datetime $value
     * @return UserFieldOptions
     */
    public function set_tms($value)
    {
        $this->tms = $value;
        return $this;
    }

    /**
     * Get tms
     *
     * @return datetime 
     */
    public function get_tms()
    {
        return $this->tms;
    }
}