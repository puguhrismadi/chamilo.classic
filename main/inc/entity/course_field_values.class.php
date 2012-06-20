<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @license see /license.txt
 * @author autogenerated
 */
class CourseFieldValues extends \Entity
{
    /**
     * @return \Entity\Repository\CourseFieldValuesRepository
     */
     public static function repository(){
        return \Entity\Repository\CourseFieldValuesRepository::instance();
    }

    /**
     * @return \Entity\CourseFieldValues
     */
     public static function create(){
        return new self();
    }

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var string $course_code
     */
    protected $course_code;

    /**
     * @var integer $field_id
     */
    protected $field_id;

    /**
     * @var text $field_value
     */
    protected $field_value;

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
     * Set course_code
     *
     * @param string $value
     * @return CourseFieldValues
     */
    public function set_course_code($value)
    {
        $this->course_code = $value;
        return $this;
    }

    /**
     * Get course_code
     *
     * @return string 
     */
    public function get_course_code()
    {
        return $this->course_code;
    }

    /**
     * Set field_id
     *
     * @param integer $value
     * @return CourseFieldValues
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
     * Set field_value
     *
     * @param text $value
     * @return CourseFieldValues
     */
    public function set_field_value($value)
    {
        $this->field_value = $value;
        return $this;
    }

    /**
     * Get field_value
     *
     * @return text 
     */
    public function get_field_value()
    {
        return $this->field_value;
    }

    /**
     * Set tms
     *
     * @param datetime $value
     * @return CourseFieldValues
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