<?php
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2008 Dokeos Latinoamerica SAC
	Copyright (c) 2006 Dokeos SPRL
	Copyright (c) 2006 Ghent University (UGent)
	Copyright (c) various contributors

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, rue du Corbeau, 108, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/
/**
 * Defines a gradebook ExerciseLink object.
 * @author Bert Steppé
 * @package dokeos.gradebook
 */
class ExerciseLink extends AbstractLink
{


// INTERNAL VARIABLES

    private $course_info = null;
    private $exercise_table = null;
    private $exercise_data = null;
    

// CONSTRUCTORS

    function ExerciseLink() {
    	$this->set_type(LINK_EXERCISE);
    }


// FUNCTIONS IMPLEMENTING ABSTRACTLINK

	/**
	 * Generate an array of exercises that a teacher hasn't created a link for.
	 * @return array 2-dimensional array - every element contains 2 subelements (id, name)
	 */
    public function get_not_created_links() {
    	if (empty($this->course_code)) {
     	die('Error in get_not_created_links() : course code not set');  		
    	}
    	$tbl_grade_links = Database :: get_main_table(TABLE_MAIN_GRADEBOOK_LINK);

		$sql = 'SELECT id,title from '.$this->get_exercise_table()
				.' WHERE id NOT IN'
				.' (SELECT ref_id FROM '.$tbl_grade_links
				.' WHERE type = '.LINK_EXERCISE
				." AND course_code = '".$this->get_course_code()."'"
				.')';

		$result = api_sql_query($sql, __FILE__, __LINE__);
		$cats=array();
		while ($data=Database::fetch_array($result)) {
			$cats[] = array ($data['id'], $data['title']);
		}
		return $cats;
    }
	/**
	 * Generate an array of all exercises available.
	 * @return array 2-dimensional array - every element contains 2 subelements (id, name)
	 */
    public function get_all_links() {
    	if (empty($this->course_code)) {
    		die('Error in get_not_created_links() : course code not set');  		
    	}
    	$course_info = api_get_course_info($this->course_code);
    	$tbl_grade_links = Database :: get_main_table(TABLE_MAIN_GRADEBOOK_LINK,$course_info['dbName']);
		$sql = 'SELECT id,title from '.$this->get_exercise_table().' where active=1';
		$result = api_sql_query($sql, __FILE__, __LINE__);

		$cats=array();
		while ($data=Database::fetch_array($result)) {
			$cats[] = array ($data['id'], $data['title']);
		}
		return $cats;
    }

    /**
     * Has anyone done this exercise yet ?
     */
    public function has_results() {
    	$tbl_stats = Database::get_statistic_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
		$sql = 'SELECT count(exe_id) AS number FROM '.$tbl_stats
				." WHERE exe_cours_id = '".$this->get_course_code()."'"
				.' AND exe_exo_id = '.$this->get_ref_id();
    	$result = api_sql_query($sql, __FILE__, __LINE__);
		$number=Database::fetch_row($result);
		return ($number[0] != 0);
    }
    
    /**
	 * Get the score of this exercise. Only the first attempts are taken into account.
	 * @param $stud_id student id (default: all students who have results - then the average is returned)
	 * @return	array (score, max) if student is given
	 * 			array (sum of scores, number of scores) otherwise
	 * 			or null if no scores available
	 */
    public function calc_score($stud_id = null) {
    	$tbl_stats = Database::get_statistic_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
    	$tbl_stats_e_attempt_recording = Database::get_statistic_table(TABLE_STATISTIC_TRACK_E_ATTEMPT_RECORDING);  
		// for 1 student
    	if (isset($stud_id))
    	{    			
    		$sql = "SELECT DISTINCT  e.exe_id, exe_result, exe_weighting, exe_user_id
					FROM $tbl_stats  as e INNER JOIN 
					$tbl_stats_e_attempt_recording as r
					ON (e.exe_id =r.exe_id)
					WHERE e.exe_cours_id = '".$this->get_course_code()."'AND					
					e.exe_id = '".$this->get_ref_id()."'  AND author != ''  AND exe_user_id = '$stud_id' ORDER BY  e.exe_id DESC ";
			$scores = api_sql_query($sql, __FILE__, __LINE__);
    		
    		if ($data=mysql_fetch_array($scores))
    		{	
    			return array ($data['exe_result'], $data['exe_weighting']);
       		}
    		else
    			return null;
    	}    	
    	// all students -> get average
    	else
    	{
    		// normal way of getting the info
		
			$sql = "SELECT DISTINCT  e.exe_id, exe_result, exe_weighting, exe_user_id
				FROM $tbl_stats  as e INNER JOIN 
				$tbl_stats_e_attempt_recording as r
				ON (e.exe_id =r.exe_id)
				WHERE e.exe_cours_id = '".$this->get_course_code()."'AND					
				e.exe_exo_id = '".$this->get_ref_id()."'  AND author != ''  ORDER BY  e.exe_id DESC ";
				    						
	    	$scores = api_sql_query($sql, __FILE__, __LINE__);

    		$students=array();  // user list, needed to make sure we only
    							// take first attempts into account
			$rescount = 0;
			$sum = 0;

			while ($data=mysql_fetch_array($scores))
			{
				if (!(array_key_exists($data['exe_user_id'],$students)))
				{
					if ($data['exe_weighting'] != 0)
					{
						$students[$data['exe_user_id']] = $data['exe_result'];
						$rescount++;
						$sum += ($data['exe_result'] / $data['exe_weighting']);
					}
				}
			}

			if ($rescount == 0)
				return null;
			else
				return array ($sum , $rescount);
    	}
    }
    
    /**
     * Get URL where to go to if the user clicks on the link.
     * First we go to exercise_jump.php and then to the result page.
     * Check this php file for more info.
     */
	public function get_link() {
		$url = api_get_path(WEB_PATH)
			.'main/gradebook/exercise_jump.php?cidReq='.$this->get_course_code().'&gradebook=view&exerciseId='.$this->get_ref_id();
		if (!api_is_allowed_to_create_course()
			&& $this->calc_score(api_get_user_id()) == null) {
		  $url .= '&amp;doexercise='.$this->get_ref_id();
        }
		return $url;
	}
    
    /**
     * Get name to display: same as exercise title
     */
    public function get_name() {
    	$data = $this->get_exercise_data();
    	return $data['title'];
    }
    
    /**
     * Get description to display: same as exercise description
     */
    public function get_description() {
    	$data = $this->get_exercise_data();
    	return $data['description'];
    }

    /**
     * Check if this still links to an exercise
     */
    public function is_valid_link() {
    	$sql = 'SELECT count(id) from '.$this->get_exercise_table()
				.' WHERE id = '.$this->get_ref_id();
		$result = api_sql_query($sql, __FILE__, __LINE__);
		$number=Database::fetch_row($result);
		return ($number[0] != 0);
    }
    
    public function get_type_name() {
    	return get_lang('DokeosExercises');
    }
    
	public function needs_name_and_description() {
		return false;
	}
	
	public function needs_max() {
		return false;
	}

	public function needs_results() {
		return false;
	}

	public function is_allowed_to_change_name() {
		return false;
	}

    
// INTERNAL FUNCTIONS
    
    /**
     * Lazy load function to get the database table of the exercise
     */
    private function get_exercise_table () {
    	$course_info = Database :: get_course_info($this->get_course_code());
		$database_name = isset($course_info['db_name']) ? $course_info['db_name'] : '';
		if ($database_name!='') {
    		if (!isset($this->exercise_table)) {
				$this->exercise_table = Database :: get_course_table(TABLE_QUIZ_TEST, $database_name);
    		}
   			return $this->exercise_table;
   		} else {
   			return '';
   		}
    }

    /**
     * Lazy load function to get the database contents of this exercise
     */
    private function get_exercise_data () {
    	$tbl_exercise=$this->get_exercise_table();
    	if ($tbl_exercise=='') {
    		return false;
    	} elseif (!isset($this->exercise_data)) {
			$sql = 'SELECT * from '.$this->get_exercise_table()
					.' WHERE id = '.$this->get_ref_id();
			$result = api_sql_query($sql, __FILE__, __LINE__);
			$this->exercise_data=Database::fetch_array($result);
    	}
    	return $this->exercise_data;
    }
}