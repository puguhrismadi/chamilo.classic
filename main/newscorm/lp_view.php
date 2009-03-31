<?php // $Id: lp_view.php,v 1.33 2006/09/12 10:20:46 yannoo Exp $
/**
==============================================================================
* This file was origially the copy of document.php, but many modifications happened since then ;
* the direct file view is not needed anymore, if the user uploads a scorm zip file, a directory
* will be automatically created for it, and the files will be uncompressed there for example ;
*
* @package dokeos.learnpath
* @author Yannick Warnier <ywarnier@beeznest.org> - redesign
* @author Denes Nagy, principal author
* @author Isthvan Mandak, several new features
* @author Roan Embrechts, code improvements and refactoring
* @license	GNU/GPL - See Dokeos license directory for details
==============================================================================
*/
/**
 * Script
 */
/*
==============================================================================
		INIT SECTION
==============================================================================
*/

$_SESSION['whereami'] = 'lp/view';
$this_section=SECTION_COURSES;

if($lp_controller_touched!=1){
	header('location: lp_controller.php?action=view&item_id='.$_REQUEST['item_id']);
}

/*
-----------------------------------------------------------
	Libraries
-----------------------------------------------------------
*/
require_once('back_compat.inc.php');
//require_once('../learnpath/learnpath_functions.inc.php');
require_once('scorm.lib.php');
require_once('learnpath.class.php');
require_once('learnpathItem.class.php');
//require_once('lp_comm.common.php'); //xajax functions

if ($is_allowed_in_course == false) api_not_allowed();
/*
-----------------------------------------------------------
	Variables
-----------------------------------------------------------
*/
//$charset = 'UTF-8';
$charset = 'ISO-8859-1';
$oLearnpath = false;
$course_code = api_get_course_id();
$user_id = api_get_user_id();
$platform_theme = api_get_setting('stylesheets'); 	// plataform's css
$my_style=$platform_theme;
//escape external variables
/*
-----------------------------------------------------------
	Header
-----------------------------------------------------------
*/
$htmlHeadXtra[] = '<script type="text/javascript" src="lp_view.lib.js"></script>';
//$htmlHeadXtra[] = $xajax->getJavascript('../inc/lib/xajax/')."\n";
$htmlHeadXtra[] = '<script src="../inc/lib/javascript/jquery.js" type="text/javascript" language="javascript"></script>'; //jQuery

$htmlHeadXtra[] = '<script language="javascript">
function cleanlog(){
  if(document.getElementById){
  	document.getElementById("log_content").innerHTML = "";
  }
}
</script>';

$htmlHeadXtra[] = '<script language="JavaScript" type="text/javascript">
  	var dokeos_xajax_handler = window.oxajax;
</script>';


$_SESSION['oLP']->error = '';
$lp_type = $_SESSION['oLP']->get_type();
$lp_item_id = $_SESSION['oLP']->get_current_item_id();
//$lp_item_id = learnpath::escape_string($_GET['item_id']);
//$_SESSION['oLP']->set_current_item($lp_item_id); // already done by lp_controller.php

//Prepare variables for the test tool (just in case) - honestly, this should disappear later on
$_SESSION['scorm_view_id'] = $_SESSION['oLP']->get_view_id();
$_SESSION['scorm_item_id'] = $lp_item_id;
//reinit exercises variables to avoid spacename clashes (see exercise tool)
if(isset($exerciseResult) or isset($_SESSION['exerciseResult']))
{
    api_session_unregister($exerciseResult);
}
unset($_SESSION['objExercise']);
unset($_SESSION['questionList']);
/**
 * Get a link to the corresponding document
 */


if (!isset($src))
 {
 	$src = '';
	switch($lp_type)
	{
		case 1:
			$_SESSION['oLP']->stop_previous_item();
			$htmlHeadXtra[] = '<script src="scorm_api.php" type="text/javascript" language="javascript"></script>';
			$prereq_check = $_SESSION['oLP']->prerequisites_match($lp_item_id);
			if($prereq_check === true){
				$src = $_SESSION['oLP']->get_link('http',$lp_item_id);
				$_SESSION['oLP']->start_current_item(); //starts time counter manually if asset
			}else{
				$src = 'blank.php?error=prerequisites';
			}
			break;
		case 2:
			//save old if asset
			$_SESSION['oLP']->stop_previous_item(); //save status manually if asset
			$htmlHeadXtra[] = '<script src="scorm_api.php" type="text/javascript" language="javascript"></script>';
			$prereq_check = $_SESSION['oLP']->prerequisites_match($lp_item_id);
			if($prereq_check === true){
				$src = $_SESSION['oLP']->get_link('http',$lp_item_id);
				$_SESSION['oLP']->start_current_item(); //starts time counter manually if asset
			}else{
			$src = 'blank.php';
			}
			break;
		case 3:
			//aicc
			$_SESSION['oLP']->stop_previous_item(); //save status manually if asset
			$htmlHeadXtra[] = '<script src="'.$_SESSION['oLP']->get_js_lib().'" type="text/javascript" language="javascript"></script>';
			$prereq_check = $_SESSION['oLP']->prerequisites_match($lp_item_id);
			if($prereq_check === true){
				$src = $_SESSION['oLP']->get_link('http',$lp_item_id);
				$_SESSION['oLP']->start_current_item(); //starts time counter manually if asset
			}else{
				$src = 'blank.php';
			}
			break;
		case 4:
			break;
	}
}

$_SESSION['oLP']->set_previous_item($lp_item_id);
$nameTools = $_SESSION['oLP']->get_name();
$save_setting = get_setting("show_navigation_menu");
global $_setting;
$_setting['show_navigation_menu'] = 'false';

$interbreadcrumb[]= array ("url"=>"lp_controller.php?action=list", "name"=> get_lang("_learning_path"));
$interbreadcrumb[]= array ("url"=>api_get_self()."?action=view&lp_id=$learnpath_id", "name" => stripslashes($_SESSION['_course']['name']));


$scorm_css_header=true;
$lp_theme_css=$_SESSION['oLP']->get_theme(); //sets the css theme of the LP this call is also use at the frames (toc, nav, message)
 
if($_SESSION['oLP']->mode == 'fullscreen')
{
	$htmlHeadXtra[] = "<script>window.open('$src','content_name','toolbar=0,location=0,status=0,scrollbars=1,resizable=1');</script>";	
	include_once('../inc/reduced_header.inc.php');
	
	//set flag to ensure lp_header.php is loaded by this script (flag is unset in lp_header.php)
	$_SESSION['loaded_lp_view'] = true;
	?>
	<frameset cols="270,*">
		<frameset rows="20,475,95,80,*">
            <frame id="header" src="lp_header.php"  border="0" frameborder="0" scrolling="no"/>
			<frame id="toc_id" name="toc_name" class="lp_toc" src="lp_toc.php" border="0" frameborder="0" scrolling="no"/>
			<frame id="nav_id" name="nav_name" class="lp_nav" src="lp_nav.php" border="0" frameborder="0" />
			<frame id="message_id" name="message_name" class="message" src="lp_message.php" border="0" frameborder="0" />
			<frame id="lp_log_id" name="lp_log_name" class="lp_log" src="lp_log.php" border="0" frameborder="0" />
		</frameset>
		<frame id="content_id_blank" name="content_name_blank" src="blank.php" border="0" frameborder="0">
		</frame>
	</frameset><noframes></noframes>
	<noframes>
	This page relies heavily on frames. If your browser doesn't support frames, please try to find a better one. Some are available for free and run on multiple platforms. We recommend you try <a href="http://www.mozilla.com/firefox/">Firefox</a>. Get it from its official website by clicking the link.
	</noframes>
<?php
}
else
{
	Display::display_header(null,'Path');

	$displayAudioRecorder = (api_get_setting('service_visio','active')=='true') ? true : false;
	//check if audio recorder needs to be in studentview
	$course_id=$_SESSION["_course"]["id"];
	if($_SESSION["status"][$course_id]==5)
	{
		$audio_recorder_studentview = true;
	}
	else
	{
		$audio_recorder_studentview = false;
	}
	//set flag to ensure lp_header.php is loaded by this script (flag is unset in lp_header.php)
	$_SESSION['loaded_lp_view'] = true;
	$audio_record_width='';
	$show_audioplayer=false;
	if ($displayAudioRecorder)
	{	// we find if there is a audioplayer
		$audio_recorder_item_id = $_SESSION['oLP']->current;
		$docs = Database::get_course_table(TABLE_DOCUMENT);
		$select = "SELECT * FROM $docs " .
				" WHERE path like BINARY '/audio/lpi".Database::escape_string($audio_recorder_item_id)."-%' AND filetype='file' " .
				" ORDER BY path DESC";
		$res = api_sql_query($select);
		if(Database::num_rows($res)>0)
		{
			$audio_record_width='85,';
			$show_audioplayer=true;
		}
		else
			$audio_record_width='';
	}
	else
		$audio_record_width='';
		
	?>
	<style type="text/css" media="screen, projection">
	/*<![CDATA[*/
	@import "<?php echo api_get_path(WEB_CODE_PATH); ?>css/<?php echo $my_style;?>/scorm.css";
	@import "<?php echo api_get_path(WEB_CODE_PATH); ?>css/<?php echo $my_style;?>/scormfs.css";
	/*]]>*/
	</style>
	<div id="learningPathLeftZone" style="float: left; width: 300px;">
        <div id="learningPathHeader" style="font-size:14px; padding-left: 17px;">
            <table>
                <tr>
                    <td>
                        <a href="lp_controller.php?action=return_to_course_homepage" target="_top" onclick="window.parent.API.save_asset();"><img src="../img/lp_arrow.gif" /></a>
                    </td>
                    <td>
                        <a class="link" href="lp_controller.php?action=return_to_course_homepage" target="_top" onclick="window.parent.API.save_asset();"><?php echo get_lang('CourseHomepageLink'); ?></a>
                    </td>
                </tr>
            </table>
        </div>
		<?php $image = '../img/lp_author_background.gif'; ?>
        <div id="image_preview">
            <table style="width: 285px; height:110px; background-image: url('.$image.');">
                <tr>
                    <td align="center">
                    <?php if ($_SESSION['oLP']->get_preview_image()!=''): ?>
                        <img alt="" src="<?php echo api_get_path(WEB_COURSE_PATH).api_get_course_path().'/upload/learning_path/images/'.$_SESSION['oLP']->get_preview_image(); ?>">
                    <?php else:
                        echo Display::display_icon('unknown_250_100.jpg',' ');
                    endif; ?>
                    </td>
                </tr>
            </table>
            <div id="author_name" class="lp_author_image">
                <?php echo $_SESSION['oLP']->get_author(); ?>
            </div>
        </div>
        <?php if ($show_audioplayer): ?>
        <div id="audiorecorder">
        <?php
        $audio_recorder_studentview = 'true';
        $audio_recorder_item_id = $_SESSION['oLP']->current;
        if(api_get_setting('service_visio','active')=='true'){
            include('audiorecorder.inc.php');
        }
        ?>
        </div>
		<?php endif; ?>
        <div class="lp_navigation_elem" >
            <table>
                <tr valign="middle">
                    <td>
                        <?php echo $_SESSION['oLP']->get_progress_bar('',-1,'',true); ?>
                    </td>
                    <td>
                        <?php echo $_SESSION['oLP']->get_navigation_bar(); ?>
                     </td>
                </tr>
            </table>
        </div>
        <div id="msg_div_id" class="message">
        <?php echo $error = $_SESSION['oLP']->error; ?>
        </div>
  		<div id="learningPathToc" class="lp_toc" style="height: 400px;width:285px;overflow-y:auto;overflow-x:hidden;padding:0;margin:0"><?php echo $_SESSION['oLP']->get_html_toc(); ?></div>
        <div id="log_content">
        </div>
        <div style="color: white;" onClick="cleanlog();">.</div>
    </div>
    <div id="learningPathRightZone" style="height: 600px; margin-left: 300px">
        <iframe id="content_id" name="content_name" src="<?php echo $src; ?>" border="0" frameborder="0" style="overflow: 100%; width: 100%"></iframe>
    </div>
    <script language="JavaScript" type="text/javascript">
	// Need to be called after the <head> to be sure window.oxajax is defined
  	var dokeos_xajax_handler = window.oxajax;
	</script>
    <script language="JavaScript" type="text/javascript">
	<!--
	var leftZoneHeightOccupied = 0;
	var rightZoneHeightOccupied = 0;
	var initialLeftZoneHeight = 0;
	var initialRightZoneHeight = 0;

	var updateContentHeight = function() {
		winHeight = (window.innerHeight != undefined ? window.innerHeight : document.documentElement.clientHeight);
		newLeftZoneHeight = winHeight - leftZoneHeightOccupied;
		newRightZoneHeight = winHeight - rightZoneHeightOccupied;
		if (newLeftZoneHeight <= initialLeftZoneHeight) {
			newLeftZoneHeight = initialLeftZoneHeight;
			newRightZoneHeight = newLeftZoneHeight + leftZoneHeightOccupied - rightZoneHeightOccupied;
		}
		if (newRightZoneHeight <= initialRightZoneHeight) {
			newRightZoneHeight = initialRightZoneHeight;
			newLeftZoneHeight = newRightZoneHeight + rightZoneHeightOccupied - leftZoneHeightOccupied;
		}
		document.getElementById('learningPathToc').style.height = newLeftZoneHeight + 'px';
		document.getElementById('learningPathRightZone').style.height = newRightZoneHeight + 'px';
		document.getElementById('content_id').style.height = newRightZoneHeight + 'px';
		if (document.body.clientHeight > winHeight) {
			document.body.style.overflow = 'auto';
		} else {
			document.body.style.overflow = 'hidden';
		}
	};

	window.onload = function() {
		initialLeftZoneHeight = document.getElementById('learningPathToc').offsetHeight;
		initialRightZoneHeight = document.getElementById('learningPathRightZone').offsetHeight;
		docHeight = document.body.clientHeight;
		leftZoneHeightOccupied = docHeight - initialLeftZoneHeight;
		rightZoneHeightOccupied = docHeight - initialRightZoneHeight;
		document.body.style.overflow = 'hidden';
		updateContentHeight();
	}

	window.onresize = updateContentHeight;
	-->
	</script>

<?php
	/*
	==============================================================================
	  FOOTER
	==============================================================================
	*/
	Display::display_footer();
}
//restore global setting
$_setting['show_navigation_menu'] = $save_setting;
?>
