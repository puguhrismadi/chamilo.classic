<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @license see /license.txt
 * @author autogenerated
 */
class TrackEExercices extends \Entity
{
    /**
     * @return \Entity\Repository\TrackEExercicesRepository
     */
     public static function repository(){
        return \Entity\Repository\TrackEExercicesRepository::instance();
    }

    /**
     * @return \Entity\TrackEExercices
     */
     public static function create(){
        return new self();
    }

    /**
     * @var integer $exe_id
     */
    protected $exe_id;

    /**
     * @var integer $exe_user_id
     */
    protected $exe_user_id;

    /**
     * @var datetime $exe_date
     */
    protected $exe_date;

    /**
     * @var string $exe_cours_id
     */
    protected $exe_cours_id;

    /**
     * @var integer $exe_exo_id
     */
    protected $exe_exo_id;

    /**
     * @var float $exe_result
     */
    protected $exe_result;

    /**
     * @var float $exe_weighting
     */
    protected $exe_weighting;

    /**
     * @var string $status
     */
    protected $status;

    /**
     * @var text $data_tracking
     */
    protected $data_tracking;

    /**
     * @var datetime $start_date
     */
    protected $start_date;

    /**
     * @var smallint $steps_counter
     */
    protected $steps_counter;

    /**
     * @var smallint $session_id
     */
    protected $session_id;

    /**
     * @var integer $orig_lp_id
     */
    protected $orig_lp_id;

    /**
     * @var integer $orig_lp_item_id
     */
    protected $orig_lp_item_id;

    /**
     * @var integer $exe_duration
     */
    protected $exe_duration;

    /**
     * @var datetime $expired_time_control
     */
    protected $expired_time_control;

    /**
     * @var integer $orig_lp_item_view_id
     */
    protected $orig_lp_item_view_id;

    /**
     * @var text $questions_to_check
     */
    protected $questions_to_check;


    /**
     * Get exe_id
     *
     * @return integer 
     */
    public function get_exe_id()
    {
        return $this->exe_id;
    }

    /**
     * Set exe_user_id
     *
     * @param integer $value
     * @return TrackEExercices
     */
    public function set_exe_user_id($value)
    {
        $this->exe_user_id = $value;
        return $this;
    }

    /**
     * Get exe_user_id
     *
     * @return integer 
     */
    public function get_exe_user_id()
    {
        return $this->exe_user_id;
    }

    /**
     * Set exe_date
     *
     * @param datetime $value
     * @return TrackEExercices
     */
    public function set_exe_date($value)
    {
        $this->exe_date = $value;
        return $this;
    }

    /**
     * Get exe_date
     *
     * @return datetime 
     */
    public function get_exe_date()
    {
        return $this->exe_date;
    }

    /**
     * Set exe_cours_id
     *
     * @param string $value
     * @return TrackEExercices
     */
    public function set_exe_cours_id($value)
    {
        $this->exe_cours_id = $value;
        return $this;
    }

    /**
     * Get exe_cours_id
     *
     * @return string 
     */
    public function get_exe_cours_id()
    {
        return $this->exe_cours_id;
    }

    /**
     * Set exe_exo_id
     *
     * @param integer $value
     * @return TrackEExercices
     */
    public function set_exe_exo_id($value)
    {
        $this->exe_exo_id = $value;
        return $this;
    }

    /**
     * Get exe_exo_id
     *
     * @return integer 
     */
    public function get_exe_exo_id()
    {
        return $this->exe_exo_id;
    }

    /**
     * Set exe_result
     *
     * @param float $value
     * @return TrackEExercices
     */
    public function set_exe_result($value)
    {
        $this->exe_result = $value;
        return $this;
    }

    /**
     * Get exe_result
     *
     * @return float 
     */
    public function get_exe_result()
    {
        return $this->exe_result;
    }

    /**
     * Set exe_weighting
     *
     * @param float $value
     * @return TrackEExercices
     */
    public function set_exe_weighting($value)
    {
        $this->exe_weighting = $value;
        return $this;
    }

    /**
     * Get exe_weighting
     *
     * @return float 
     */
    public function get_exe_weighting()
    {
        return $this->exe_weighting;
    }

    /**
     * Set status
     *
     * @param string $value
     * @return TrackEExercices
     */
    public function set_status($value)
    {
        $this->status = $value;
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function get_status()
    {
        return $this->status;
    }

    /**
     * Set data_tracking
     *
     * @param text $value
     * @return TrackEExercices
     */
    public function set_data_tracking($value)
    {
        $this->data_tracking = $value;
        return $this;
    }

    /**
     * Get data_tracking
     *
     * @return text 
     */
    public function get_data_tracking()
    {
        return $this->data_tracking;
    }

    /**
     * Set start_date
     *
     * @param datetime $value
     * @return TrackEExercices
     */
    public function set_start_date($value)
    {
        $this->start_date = $value;
        return $this;
    }

    /**
     * Get start_date
     *
     * @return datetime 
     */
    public function get_start_date()
    {
        return $this->start_date;
    }

    /**
     * Set steps_counter
     *
     * @param smallint $value
     * @return TrackEExercices
     */
    public function set_steps_counter($value)
    {
        $this->steps_counter = $value;
        return $this;
    }

    /**
     * Get steps_counter
     *
     * @return smallint 
     */
    public function get_steps_counter()
    {
        return $this->steps_counter;
    }

    /**
     * Set session_id
     *
     * @param smallint $value
     * @return TrackEExercices
     */
    public function set_session_id($value)
    {
        $this->session_id = $value;
        return $this;
    }

    /**
     * Get session_id
     *
     * @return smallint 
     */
    public function get_session_id()
    {
        return $this->session_id;
    }

    /**
     * Set orig_lp_id
     *
     * @param integer $value
     * @return TrackEExercices
     */
    public function set_orig_lp_id($value)
    {
        $this->orig_lp_id = $value;
        return $this;
    }

    /**
     * Get orig_lp_id
     *
     * @return integer 
     */
    public function get_orig_lp_id()
    {
        return $this->orig_lp_id;
    }

    /**
     * Set orig_lp_item_id
     *
     * @param integer $value
     * @return TrackEExercices
     */
    public function set_orig_lp_item_id($value)
    {
        $this->orig_lp_item_id = $value;
        return $this;
    }

    /**
     * Get orig_lp_item_id
     *
     * @return integer 
     */
    public function get_orig_lp_item_id()
    {
        return $this->orig_lp_item_id;
    }

    /**
     * Set exe_duration
     *
     * @param integer $value
     * @return TrackEExercices
     */
    public function set_exe_duration($value)
    {
        $this->exe_duration = $value;
        return $this;
    }

    /**
     * Get exe_duration
     *
     * @return integer 
     */
    public function get_exe_duration()
    {
        return $this->exe_duration;
    }

    /**
     * Set expired_time_control
     *
     * @param datetime $value
     * @return TrackEExercices
     */
    public function set_expired_time_control($value)
    {
        $this->expired_time_control = $value;
        return $this;
    }

    /**
     * Get expired_time_control
     *
     * @return datetime 
     */
    public function get_expired_time_control()
    {
        return $this->expired_time_control;
    }

    /**
     * Set orig_lp_item_view_id
     *
     * @param integer $value
     * @return TrackEExercices
     */
    public function set_orig_lp_item_view_id($value)
    {
        $this->orig_lp_item_view_id = $value;
        return $this;
    }

    /**
     * Get orig_lp_item_view_id
     *
     * @return integer 
     */
    public function get_orig_lp_item_view_id()
    {
        return $this->orig_lp_item_view_id;
    }

    /**
     * Set questions_to_check
     *
     * @param text $value
     * @return TrackEExercices
     */
    public function set_questions_to_check($value)
    {
        $this->questions_to_check = $value;
        return $this;
    }

    /**
     * Get questions_to_check
     *
     * @return text 
     */
    public function get_questions_to_check()
    {
        return $this->questions_to_check;
    }
}