<?php
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Olivier Brouckaert

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: Dokeos, 181 rue Royale, B-1000 Brussels, Belgium, info@dokeos.com
==============================================================================
*/
/**
============================================================================== 
	@author Bart Mollet
*	@package dokeos.admin
============================================================================== 
*/
/*
==============================================================================
		INIT SECTION
==============================================================================
*/
$langFile = 'survey';

require ('../inc/global.inc.php');
//api_protect_admin_script();
require_once (api_get_path(LIBRARY_PATH)."/course.lib.php");
require_once (api_get_path(LIBRARY_PATH)."/surveymanager.lib.php");
$status = surveymanager::get_status();
if($status==5)
{
api_protect_admin_script();
}
$interbredcrump[] = array ("url" => "survey_list.php", "name" => get_lang('Survey'));
$cidReq = $_REQUEST['cidReq'];
$db_name = $_REQUEST['db_name'];
$tool_name = get_lang('SelectQuestion');
$Sname = get_lang('SurveyName');
$GName = get_lang('groupname');
$Author = get_lang('Author');
$surveyid=$_REQUEST['surveyid'];
$groupid=$_REQUEST['groupid'];
//$sname =surveymanager::get_surveyname($surveyid);
$gide=$_REQUEST['course'];
$gid1=explode(",",$gide);
$table_question = Database :: get_course_table('questions');
if($gide)
{
	$gid1=$gid1;
}
else
{
$gid1=explode(",",$_REQUEST['gid1']);  
}
for($r=0;$r<count($gid1);$r++)
{
	  if($r<count($gid1)-1)
	  $str.=$gid1[$r].",";
	  else
      $str.=$gid1[$r]; 
   
}
if(isset($_POST['back']))
{
	$db_name = $_POST['db_name'];
	$cidReq=$_GET['cidReq'];
    header("location:create_from_existing_survey.php?cidReq=$cidReq&db_name=$db_name&surveyid=$surveyid");
}
if(isset($_POST['importquestion']))
{
  $surveyid = $_POST['surveyid'];
  $cidReq=$_GET['cidReq'];
  $selectcount=count($_POST['question']);	
  if($selectcount<=0)
   {
	$error_message=$error_message=get_lang("PleaseSelectAChoice");		
   }
 else
  {
	$qid = $_POST['question'];
	$db_name = $_POST['db_name'];
	$qids = implode(",",$qid);
	header("location:attach_question.php?surveyid=$surveyid&qid=$qids&cidReq=$cidReq&db_name=$db_name");
	exit;
  }
}
Display :: display_header($tool_name);
if( isset($error_message) )
{
	Display::display_error_message($error_message);	
}
?>
<SCRIPT LANGUAGE="JavaScript">
function displayTemplate(url){
	window.open(url, 'popup', 'width=600,height=400,toolbar = no, status = no');
}
</script>
<?php	
	api_display_tool_title($tool_name);
?>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>?cidReq=<?=$cidReq?>">
		<input type="hidden" name="action" value="add_survey"/>
		<input type="hidden" name="surveyid" value="<?=$surveyid?>">
		<input type="hidden" name="groupid" value="<?=$groupid?>">
		<input type="hidden" name="gid1" value="<?=$str?>">
		<input type="hidden" name="db_name" value="<?=$db_name?>">
<? 
		$questions = array ();
		$cidReq=$_GET['cidReq'];
		$endloop=count($gid1);
		$datacount=0;
		$parameters = array (); 
		$parameters['surveyid']=$surveyid;
		$parameters['groupid']=$groupid;
		$parameters['cidReq']=$cidReq;
		$parameters['db_name']=$db_name;
		$parameters['gid1']=$str;
		for($i=0;$i<$endloop;$i++)
		{
			$gidi=$gid1[$i];
			$sql = "SELECT * FROM $db_name.questions WHERE gid='$gidi'";		
			
			/*
			$parameters = array ('gidi' => $gidi); 
			$parameters['surveyid']=$surveyid;
			$parameters['groupid']=$groupid;
			$parameters['cidReq']=$cidReq;
			$parameters['course']=$_REQUEST['course'];
			$parameters['db_name']=$db_name;
			*/
			$res = api_sql_query($sql,__FILE__,__LINE__);			
			while ($obj = mysql_fetch_object($res))
			{
				$question = array ();
				$question[] = '<input type="checkbox" name="question[]" value="'.$obj->qid.'"/>';
				$question[] = $obj->caption;
				$question[] = $obj->qtype;
				$groupid = $obj->gid;
				$GName = surveymanager::get_groupname($db_name,$groupid);
				$question[] = $GName;
				$sid = surveymanager::get_surveyid($db_name,$groupid);
				$sname = surveymanager::get_surveyname($db_name,$sid);
				$question[] = $sname;
				/**********for displaying the 'edit' 'delete' etc. buttons***********/
				$url = "default.php?qid=".$obj->qid."&qtype=".$obj->qtype."&cidReq=".$cidReq."&surveyid=".$surveyid."&groupid=".$groupid."&db_name=".$db_name;
				$question[] = "<a href=\"#\" onClick=\"displayTemplate('".$url."')\"><img src=\"../img/info_small.gif\" border=\"0\" align=\"absmiddle\" alt=\"".get_lang('ViewQues')."\" /></a>";
				$questions[] = $question;
				$datacount++;
			}
		}
		$table_header[] = array (' ', false);
		$table_header[] = array (get_lang('Question'), true);
		$table_header[] = array (get_lang('QuestionType1'), true);
		$table_header[] = array (get_lang('Group'),true);
		$table_header[] = array	(get_lang('surveyname'),true);
		$table_header[] = array('', false);
		
		if($datacount>0)
		Display :: display_sortable_table($table_header, $questions, array (), array (), $parameters);
		else
		{
			$noquestions=get_lang("NoQuestionAvailableInThisGroup");
			api_display_tool_title($noquestions);
		}
?>
		<table>
		<tr>		
		<td><input type="submit" name="back" value="Back"></td>
		<td><input type="submit" name="importquestion" value="<? echo get_lang('ImportQuestion');?>"></td>
		</tr>
		</table>
		</form>	
<?php
	Display :: display_footer();
?> 