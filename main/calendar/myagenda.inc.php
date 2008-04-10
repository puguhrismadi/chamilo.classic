<?php //$id: $
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: Dokeos, 181 rue Royale, B-1000 Brussels, Belgium, info@dokeos.com
==============================================================================
	@author: Patrick Cool <patrick.cool@UGent.be>, Ghent University
	@author: Toon Van Hoecke <toon.vanhoecke@ugent.be>, Ghent University
	@author: Eric Remy (initial version)
	@version: 2.2 alpha
	@description: 	this file generates a general agenda of all items of the
					courses the user is registered for
==============================================================================
	version info:
	-------------
	-> version 2.2 : Patrick Cool, patrick.cool@ugent.be, november 2004
	Personal Agenda added. The user can add personal agenda items. The items
	are stored in a dokeos_user database because it is not course or platform
	based. A personal agenda view was also added. This lists all the personal
	agenda items of that user.

	-> version 2.1 : Patrick Cool, patrick.cool@ugent.be, , oktober 2004
	This is the version that works with the Group based Agenda tool.

	-> version 2.0 (alpha): Patrick Cool, patrick.cool@ugent.be, , oktober 2004
	The 2.0 version introduces besides the month view also a week- and day view.
	In the 2.5 (final) version it will be possible for the student to add his/her
	own agenda items. The platform administrator can however decide if the students
	are allowed to do this or not.
	The alpha version only contains the three views. The personal agenda feature is
	not yet completely finished. There are however already some parts of the code
	for adding a personal agenda item present.
	this code was not released in an official dokeos but was only used in the offical
	server of the Ghent University where it underwent serious testing

	-> version 1.5: Toon Van Hoecke, toon.vanhoecke@ugent.be, december 2003

	-> version 1.0: Eric Remy, eremy@rmwc.edu, 6 Oct 2003
	The tool was initially called master-calendar as it collects all the calendar
	items of all the courses one is subscribed to. It was very soon integrated in
	Dokeos as this was a really basic and very usefull tool.
*/

/**
 * Settings (you may alter this at will
 */
$setting_agenda_link = 'coursecode'; // valid values are coursecode and icon

/**
 *	This function retrieves all the agenda items of all the course of the user
 */
function get_agendaitems($courses_dbs, $month, $year)
{
	global $_user;
	global $_configuration;
	global $setting_agenda_link;

	$items = array ();
	// get agenda-items for every course
	foreach ($courses_dbs as $key => $array_course_info)
	{
		//databases of the courses
		$TABLEAGENDA = Database :: get_course_table(TABLE_AGENDA, $array_course_info["db"]);
		$TABLE_ITEMPROPERTY = Database :: get_course_table(TABLE_ITEM_PROPERTY, $array_course_info["db"]);

		$group_memberships = GroupManager :: get_group_ids($array_course_info["db"], $_user['user_id']);
		// if the user is administrator of that course we show all the agenda items
		if ($array_course_info['status'] == '1')
		{
			//echo "course admin";
			$sqlquery = "SELECT
										DISTINCT agenda.*, item_property.*
										FROM ".$TABLEAGENDA." agenda,
											 ".$TABLE_ITEMPROPERTY." item_property
										WHERE agenda.id = item_property.ref   ".$show_all_current."
										AND MONTH(agenda.start_date)='".$month."'
										AND YEAR(agenda.start_date)='".$year."'
										AND item_property.tool='".TOOL_CALENDAR_EVENT."'
										AND item_property.visibility='1'
										GROUP BY agenda.id
										ORDER BY start_date ".$sort;
		}
		// if the user is not an administrator of that course
		else
		{
			//echo "GEEN course admin";
			if (is_array($group_memberships))
			{
				$sqlquery = "SELECT
													agenda.*, item_property.*
													FROM ".$TABLEAGENDA." agenda,
														".$TABLE_ITEMPROPERTY." item_property
													WHERE `agenda`.`id` = `item_property`.`ref`   ".$show_all_current."
													AND MONTH(`agenda`.`start_date`)='".$month."'
													AND YEAR(`agenda`.`start_date`)='".$year."'
													AND `item_property`.`tool`='".TOOL_CALENDAR_EVENT."'
													AND	( `item_property`.`to_user_id`='".$_user['user_id']."' OR `item_property`.`to_group_id` IN (0, ".implode(", ", $group_memberships).") )
													AND `item_property`.`visibility`='1'
													ORDER BY start_date ".$sort;
			}
			else
			{
				$sqlquery = "SELECT
													agenda.*, item_property.*
													FROM ".$TABLEAGENDA." agenda,
														".$TABLE_ITEMPROPERTY." item_property
													WHERE `agenda`.`id` = `item_property`.`ref`   ".$show_all_current."
													AND MONTH(`agenda`.`start_date`)='".$month."'
													AND YEAR(`agenda`.`start_date`)='".$year."'
													AND `item_property`.`tool`='".TOOL_CALENDAR_EVENT."'
													AND ( `item_property`.`to_user_id`='".$_user['user_id']."' OR `item_property`.`to_group_id`='0')
													AND `item_property`.`visibility`='1'
													ORDER BY start_date ".$sort;
			}
		}

		$result = api_sql_query($sqlquery, __FILE__, __LINE__);
		while ($item = mysql_fetch_array($result))
		{
			$agendaday = date("j",strtotime($item['start_date']));
			$time= date("H:i",strtotime($item['start_date']));
			$URL = api_get_path(WEB_PATH)."main/calendar/agenda.php?cidReq=".urlencode($array_course_info["code"])."&amp;day=$agendaday&amp;month=$month&amp;year=$year#$agendaday"; // RH  //Patrick Cool: to highlight the relevant agenda item
			if ($setting_agenda_link == 'coursecode')
			{
				$agenda_link = $array_course_info["visual_code"];
			}
			else 
			{
				$agenda_link = Display::return_icon('course_home.gif');
			}
			$items[$agendaday][$item['start_time']] .= "<i>".$time."</i> <a href=\"$URL\" title=\"".$array_course_info["name"]."\">".$agenda_link."</a>  ".$item['title']."<br />";
		}
	}
	// sorting by hour for every day
	$agendaitems = array ();
	while (list ($agendaday, $tmpitems) = each($items))
	{
		sort($tmpitems);
		while (list ($key, $val) = each($tmpitems))
		{
			$agendaitems[$agendaday] .= $val;
		}
	}
	//print_r($agendaitems);
	return $agendaitems;
}
/**
 * Show the monthcalender of the given month
 */
function display_monthcalendar($agendaitems, $month, $year, $weekdaynames, $monthName)
{
	global $DaysShort,$course_path;
	//Handle leap year
	$numberofdays = array (0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	if (($year % 400 == 0) or ($year % 4 == 0 and $year % 100 <> 0))
		$numberofdays[2] = 29;
	//Get the first day of the month
	$dayone = getdate(mktime(0, 0, 0, $month, 1, $year));
	//Start the week on monday
	$startdayofweek = $dayone['wday'] <> 0 ? ($dayone['wday'] - 1) : 6;
	$backwardsURL = api_get_self()."?coursePath=".urlencode($course_path)."&amp;courseCode=".Security::remove_XSS($_GET['courseCode'])."&amp;action=view&amp;view=month&amp;month=". ($month == 1 ? 12 : $month -1)."&amp;year=". ($month == 1 ? $year -1 : $year);
	$forewardsURL = api_get_self()."?coursePath=".urlencode($course_path)."&amp;courseCode=".Security::remove_XSS($_GET['courseCode'])."&amp;action=view&amp;view=month&amp;month=". ($month == 12 ? 1 : $month +1)."&amp;year=". ($month == 12 ? $year +1 : $year);

	echo "<table id=\"agenda_list\">\n", "<tr class=\"title\">\n", "<td width=\"10%\"><a href=\"", $backwardsURL, "\">&#171;</a></td>\n", "<td width=\"80%\" colspan=\"5\">", $monthName, " ", $year, "</td>\n", "<td width=\"10%\"><a href=\"", $forewardsURL, "\">&#187;</a></td>\n", "</tr>\n";

	echo "<tr>\n";
	for ($ii = 1; $ii < 8; $ii ++)
	{
		echo "<td class=\"weekdays\">", $DaysShort[$ii % 7], "</td>\n";
	}
	echo "</tr>\n";
	$curday = -1;
	$today = getdate();
	while ($curday <= $numberofdays[$month])
	{
		echo "<tr>\n";
		for ($ii = 0; $ii < 7; $ii ++)
		{
			if (($curday == -1) && ($ii == $startdayofweek))
			{
				$curday = 1;
			}
			if (($curday > 0) && ($curday <= $numberofdays[$month]))
			{
				$bgcolor = $ii < 5 ? $class = "class=\"days_week\"" : $class = "class=\"days_weekend\"";
				$dayheader = "<b>$curday</b>";
				if (($curday == $today[mday]) && ($year == $today[year]) && ($month == $today[mon]))
				{
					$dayheader = "$curday - ".get_lang("Today")."<br />";
					$class = "class=\"days_today\"";
				}
				echo "<td ".$class.">", "".$dayheader;
				if (!empty($agendaitems[$curday]))
				{
					echo "<span class=\"agendaitem\">".$agendaitems[$curday]."</span>";
				}
				echo "</td>\n";
				$curday ++;
			}
			else
			{
				echo "<td>&nbsp;</td>\n";
			}
		}
		echo "</tr>\n";
	}
	echo "</table>\n";
}
/**
 * Show the mini calender of the given month
 */
function display_minimonthcalendar($agendaitems, $month, $year, $monthName)
{
	global $DaysShort,$course_path;
	//Handle leap year
	$numberofdays = array (0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	if (($year % 400 == 0) or ($year % 4 == 0 and $year % 100 <> 0))
		$numberofdays[2] = 29;
	//Get the first day of the month
	$dayone = getdate(mktime(0, 0, 0, $month, 1, $year));
	//Start the week on monday
	$startdayofweek = $dayone['wday'] <> 0 ? ($dayone['wday'] - 1) : 6;
	$backwardsURL = api_get_self()."?coursePath=".urlencode($course_path)."&amp;courseCode=".Security::remove_XSS($_GET['courseCode'])."&amp;month=". ($month == 1 ? 12 : $month -1)."&amp;year=". ($month == 1 ? $year -1 : $year);
	$forewardsURL = api_get_self()."?coursePath=".urlencode($course_path)."&amp;courseCode=".Security::remove_XSS($_GET['courseCode'])."&amp;month=". ($month == 12 ? 1 : $month +1)."&amp;year=". ($month == 12 ? $year +1 : $year);

	echo "<table id=\"smallcalendar\">\n", "<tr class=\"title\">\n", "<td width=\"10%\"><a href=\"", $backwardsURL, "\">&#171;</a></td>\n", "<td width=\"80%\" colspan=\"5\">", $monthName, " ", $year, "</td>\n", "<td width=\"10%\"><a href=\"", $forewardsURL, "\">&#187;</a></td>\n", "</tr>\n";

	echo "<tr>\n";
	for ($ii = 1; $ii < 8; $ii ++)
	{
		echo "<td class=\"weekdays\">", $DaysShort[$ii % 7], "</td>\n";
	}
	echo "</tr>\n";
	$curday = -1;
	$today = getdate();
	while ($curday <= $numberofdays[$month])
	{
		echo "<tr>\n";
		for ($ii = 0; $ii < 7; $ii ++)
		{
			if (($curday == -1) && ($ii == $startdayofweek))
			{
				$curday = 1;
			}
			if (($curday > 0) && ($curday <= $numberofdays[$month]))
			{
				$bgcolor = $ii < 5 ? $class = "class=\"days_week\"" : $class = "class=\"days_weekend\"";
				$dayheader = "$curday";
				if (($curday == $today[mday]) && ($year == $today[year]) && ($month == $today[mon]))
				{
					$dayheader = "$curday";
					$class = "class=\"days_today\"";
				}
				echo "\t<td ".$class.">";
				if ($agendaitems[$curday] <> "")
				{
					echo "<a href=\"".api_get_self()."?action=view&amp;view=day&amp;day=".$curday."&amp;month=".$month."&amp;year=".$year."\">".$dayheader."</a>";
				}
				else
				{
					echo $dayheader;
				}
				// "a".$dayheader." <span class=\"agendaitem\">".$agendaitems[$curday]."</span>\n";
				echo "</td>\n";
				$curday ++;
			}
			else
			{
				echo "<td>&nbsp;</td>\n";
			}
		}
		echo "</tr>\n";
	}
	echo "</table>\n";
}
/**
 *	Display the weekly view of the calendar
 */ 
function display_weekcalendar($agendaitems, $month, $year, $weekdaynames, $monthName)
{
	global $DaysShort,$course_path;
	global $MonthsLong;
	// timestamp of today
	$today = time();
	$day_of_the_week = date("w", $today);
	$thisday_of_the_week = date("w", $today);
	// the week number of the year
	$week_number = date("W", $today);
	$thisweek_number = $week_number;
	// if we moved to the next / previous week we have to recalculate the $today variable
	if ($_GET['week'])
	{
		$today = mktime(0, 0, 0, 1, 1, $year);
		$today = $today + (((int)$_GET['week']-1) * (7 * 24 * 60 * 60));
		$week_number = date("W", $today);
	}
	// calculating the start date of the week
	// the date of the monday of this week is the timestamp of today minus
	// number of days that have already passed this week * 24 hours * 60 minutes * 60 seconds
	$current_day = date("j", $today); // Day of the month without leading zeros (1 to 31) of today
	$day_of_the_week = date("w", $today); // Numeric representation of the day of the week	0 (for Sunday) through 6 (for Saturday) of today
	$timestamp_first_date_of_week = $today - (($day_of_the_week -1) * 24 * 60 * 60); // timestamp of the monday of this week
	$timestamp_last_date_of_week = $today + ((7 - $day_of_the_week) * 24 * 60 * 60); // timestamp of the sunday of this week
	$backwardsURL = api_get_self()."?coursePath=".urlencode($course_path)."&amp;courseCode=".Security::remove_XSS($_GET['courseCode'])."&amp;action=view&amp;view=week&amp;week=". ($week_number -1);
	$forewardsURL = api_get_self()."?coursePath=".urlencode($course_path)."&amp;courseCode=".Security::remove_XSS($_GET['courseCode'])."&amp;action=view&amp;view=week&amp;week=". ($week_number +1);
	echo "<table id=\"agenda_list\">\n";
	// The title row containing the the week information (week of the year (startdate of week - enddate of week)
	echo "<tr class=\"title\">\n";
	echo "<td width=\"10%\"><a href=\"", $backwardsURL, "\">&#171;</a></td>\n";
	echo "<td colspan=\"5\">".get_lang("Week")." ".$week_number;
	echo " (".$DaysShort['1']." ".date("j", $timestamp_first_date_of_week)." ".$MonthsLong[date("n", $timestamp_first_date_of_week) - 1]." ".date("Y", $timestamp_first_date_of_week)." - ".$DaysShort['0']." ".date("j", $timestamp_last_date_of_week)." ".$MonthsLong[date("n", $timestamp_last_date_of_week) - 1]." ".date("Y", $timestamp_last_date_of_week).')';
	echo "</td>";
	echo "<td width=\"10%\"><a href=\"", $forewardsURL, "\">&#187;</a></td>\n", "</tr>\n";
	// The second row containing the short names of the days of the week
	echo "<tr>\n";
	// this is the Day of the month without leading zeros (1 to 31) of the monday of this week
	$tmp_timestamp = $timestamp_first_date_of_week;
	for ($ii = 1; $ii < 8; $ii ++)
	{
		$is_today = ($ii == $thisday_of_the_week AND (!isset($_GET['week']) OR $_GET['week']==$thisweek_number));
		echo "\t<td class=\"weekdays\">";
		if ($is_today)
		{
			echo "<font color=#CC3300>";
		}
		echo $DaysShort[$ii % 7]." ".date("j", $tmp_timestamp)." ".$MonthsLong[date("n", $tmp_timestamp) - 1];
		if ($is_today)
		{
			echo "</font>";
		}
		echo "</td>\n";
		// we 24 hours * 60 minutes * 60 seconds to the $tmp_timestamp
		$array_tmp_timestamp[] = $tmp_timestamp;
		$tmp_timestamp = $tmp_timestamp + (24 * 60 * 60);
	}
	echo "</tr>\n";
	// the table cells containing all the entries for that day
	echo "<tr>\n";
	$counter = 0;
	foreach ($array_tmp_timestamp as $key => $value)
	{
		if ($counter < 5)
		{
			$class = "class=\"days_week\"";
		}
		else
		{
			$class = "class=\"days_weekend\"";
		}
		if ($counter == $thisday_of_the_week -1 AND (!isset($_GET['week']) OR $_GET['week']==$thisweek_number))
		{
			$class = "class=\"days_today\"";
		}

		echo "\t<td ".$class.">";
		echo "<span class=\"agendaitem\">".$agendaitems[date("j", $value)]."&nbsp;</span> ";
		echo "</td>\n";
		$counter ++;
	}
	echo "</tr>\n";
	echo "</table>\n";
}
/**
 * Show the mini calender of the given month
 */
function display_daycalendar($agendaitems, $day, $month, $year, $weekdaynames, $monthName)
{
	global $DaysShort, $DaysLong, $course_path;
	global $MonthsLong;
	global $query;

	// timestamp of today
	$today = mktime();
	$nextday = $today + (24 * 60 * 60);
	$previousday = $today - (24 * 60 * 60);
	// the week number of the year
	$week_number = date("W", $today);
	// if we moved to the next / previous day we have to recalculate the $today variable
	if ($_GET['day'])
	{
		$today = mktime(0, 0, 0, $month, $day, $year);
		$nextday = $today + (24 * 60 * 60);
		$previousday = $today - (24 * 60 * 60);
		$week_number = date("W", $today);
	}
	// calculating the start date of the week
	// the date of the monday of this week is the timestamp of today minus
	// number of days that have already passed this week * 24 hours * 60 minutes * 60 seconds
	$current_day = date("j", $today); // Day of the month without leading zeros (1 to 31) of today
	$day_of_the_week = date("w", $today); // Numeric representation of the day of the week	0 (for Sunday) through 6 (for Saturday) of today
	//$timestamp_first_date_of_week=$today-(($day_of_the_week-1)*24*60*60); // timestamp of the monday of this week
	//$timestamp_last_date_of_week=$today+((7-$day_of_the_week)*24*60*60); // timestamp of the sunday of this week
	// we are loading all the calendar items of all the courses for today
	echo "<table id=\"agenda_list\">\n";
	// the forward and backwards url
	$backwardsURL = api_get_self()."?coursePath=".urlencode($course_path)."&amp;courseCode=".Security::remove_XSS($_GET['courseCode'])."&amp;action=view&amp;view=day&amp;day=".date("j", $previousday)."&amp;month=".date("n", $previousday)."&amp;year=".date("Y", $previousday);
	$forewardsURL = api_get_self()."?coursePath=".urlencode($course_path)."&amp;courseCode=".Security::remove_XSS($_GET['courseCode'])."&amp;action=view&amp;view=day&amp;day=".date("j", $nextday)."&amp;month=".date("n", $nextday)."&amp;year=".date("Y", $nextday);
	// The title row containing the day
	echo "<tr class=\"title\">\n", "<td width=\"10%\"><a href=\"", $backwardsURL, "\">&#171;</a></td>\n", "<td>";
	echo $DaysLong[$day_of_the_week]." ".date("j", $today)." ".$MonthsLong[date("n", $today) - 1]." ".date("Y", $today);
	echo "</td>";
	echo "<td width=\"10%\"><a href=\"", $forewardsURL, "\">&#187;</a></td>\n";
	echo "</tr>\n";
	// the rows for each half an hour
	for ($i = 10; $i < 48; $i ++)
	{
		echo "<tr>\n";
		echo "\t";
		if ($i % 2 == 0)
		{
			$class = "class=\"alternativeBgLight\"";
			echo ("<td $class valign=\"top\" width=\"75\">". (($i) / 2)." ".get_lang("HourShort")." 00</td>\n");
		}
		else
		{
			$class = "";
			echo ("<td valign=\"top\" width=\"75\">". ((($i) / 2) - (1 / 2))." ".get_lang("HourShort")." 30</td>\n");
		}
		echo "\t<td $class valign=\"top\" colspan=\"2\">\n";
		if (is_array($agendaitems[$i]))
		{
			foreach ($agendaitems[$i] as $key => $value)
			{
				echo $value;
			}
		}
		else
		{
			echo $agendaitems[$i];
		}
		echo "\t</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";
}
/**
 * Show the monthcalender of the given month
 */
function get_day_agendaitems($courses_dbs, $month, $year, $day)
{
	global $_user;
	global $_configuration;

	$items = array ();

	// get agenda-items for every course
	//$query=api_sql_query($sql_select_courses);
	foreach ($courses_dbs as $key => $array_course_info)
	{
		//databases of the courses
		$TABLEAGENDA = Database :: get_course_table(TABLE_AGENDA, $array_course_info["db"]);
		$TABLE_ITEMPROPERTY = Database :: get_course_table(TABLE_ITEM_PROPERTY, $array_course_info["db"]);

		// getting all the groups of the user for the current course
		$group_memberships = GroupManager :: get_group_ids($array_course_info["db"], $_user['user_id']);
		// if the user is administrator of that course we show all the agenda items
		if ($array_course_info['status'] == '1')
		{
			//echo "course admin";
			$sqlquery = "SELECT
										DISTINCT agenda.*, item_property.*
										FROM ".$TABLEAGENDA." agenda,
											".$TABLE_ITEMPROPERTY." item_property
										WHERE `agenda`.`id` = `item_property`.`ref`   ".$show_all_current."
										AND DAYOFMONTH(start_date)='".$day."' AND MONTH(start_date)='".$month."' AND YEAR(start_date)='".$year."'
										AND `item_property`.`tool`='".TOOL_CALENDAR_EVENT."'
										AND `item_property`.`visibility`='1'
										GROUP BY agenda.id
										ORDER BY start_date ".$sort;
		}
		// if the user is not an administrator of that course
		else
		{
			//echo "GEEN course admin";
			if (is_array($group_memberships))
			{
				$sqlquery = "SELECT
													agenda.*, item_property.*
													FROM ".$TABLEAGENDA." agenda,
														".$TABLE_ITEMPROPERTY." item_property
													WHERE `agenda`.`id` = `item_property`.`ref`   ".$show_all_current."
													AND DAYOFMONTH(start_date)='".$day."' AND MONTH(start_date)='".$month."' AND YEAR(start_date)='".$year."'
													AND `item_property`.`tool`='".TOOL_CALENDAR_EVENT."'
													AND	( `item_property`.`to_user_id`='".$_user['user_id']."' OR `item_property`.`to_group_id` IN (0, ".implode(", ", $group_memberships).") )
													AND `item_property`.`visibility`='1'
													ORDER BY start_date ".$sort;
			}
			else
			{
				$sqlquery = "SELECT
													agenda.*, item_property.*
													FROM ".$TABLEAGENDA." agenda,
														".$TABLE_ITEMPROPERTY." item_property
													WHERE `agenda`.`id` = `item_property`.`ref`   ".$show_all_current."
													AND DAYOFMONTH(start_date)='".$day."' AND MONTH(start_date)='".$month."' AND YEAR(start_date)='".$year."'
													AND `item_property`.`tool`='".TOOL_CALENDAR_EVENT."'
													AND ( `item_property`.`to_user_id`='".$_user['user_id']."' OR `item_property`.`to_group_id`='0')
													AND `item_property`.`visibility`='1'
													ORDER BY start_date ".$sort;
			}
		}
		//$sqlquery = "SELECT * FROM $agendadb WHERE DAYOFMONTH(day)='$day' AND month(day)='$month' AND year(day)='$year'";
		//echo "abc";
		//echo $sqlquery;
		$result = api_sql_query($sqlquery, __FILE__, __LINE__);
		//echo mysql_num_rows($result);
		while ($item = mysql_fetch_array($result))
		{
			// in the display_daycalendar function we use $i (ranging from 0 to 47) for each halfhour
			// we want to know for each agenda item for this day to wich halfhour it must be assigned
			list ($datepart, $timepart) = split(" ", $item['start_date']);
			list ($year, $month, $day) = explode("-", $datepart);
			list ($hours, $minutes, $seconds) = explode(":", $timepart);

			$halfhour = 2 * $hours;
			if ($minutes >= '30')
			{
				$halfhour = $halfhour +1;
			}
			//$URL = $_configuration['root_web'].$mycours["dir"]."/";
			$URL = $_configuration['root_web']."main/calendar/agenda.php?cidReq=".urlencode($array_course_info["code"])."&amp;day=$day&amp;month=$month&amp;year=$year#$day"; // RH  //Patrick Cool: to highlight the relevant agenda item
			$items[$halfhour][] .= "<i>".$hours.":".$minutes."</i> <a href=\"$URL\" title=\"".$array_course_info['name']."\">".$array_course_info['visual_code']."</a>  ".$item['title']."<br />";
		}
	}
	// sorting by hour for every day
	/*$agendaitems = array();
	while (list($agendaday, $tmpitems) = each($items))
	{
		sort($tmpitems);
		while (list($key,$val) = each($tmpitems))
		{
			$agendaitems[$agendaday].=$val;
		}
	}*/
	$agendaitems = $items;
	//print_r($agendaitems);
	return $agendaitems;
}
/**
 * Return agenda items of the week
 */
function get_week_agendaitems($courses_dbs, $month, $year, $week = '')
{
	global $TABLEAGENDA, $TABLE_ITEMPROPERTY;
	global $_user;
	global $_configuration;

	$items = array ();
	// The default value of the week
	if ($week == '')
	{
		$week_number = date("W", time());
	}
	else
	{
		$week_number = $week;
	}
	$start_end = calculate_start_end_of_week($week_number, $year);
	$start_filter = $start_end['start']['year']."-".$start_end['start']['month']."-".$start_end['start']['day'];
	$end_filter = $start_end['end']['year']."-".$start_end['end']['month']."-".$start_end['end']['day'];
	// get agenda-items for every course
	foreach ($courses_dbs as $key => $array_course_info)
	{
		//databases of the courses
		$TABLEAGENDA = Database :: get_course_table(TABLE_AGENDA, $array_course_info["db"]);
		$TABLE_ITEMPROPERTY = Database :: get_course_table(TABLE_ITEM_PROPERTY, $array_course_info["db"]);

		// getting all the groups of the user for the current course
		$group_memberships = GroupManager :: get_group_ids($array_course_info["db"], $_user['user_id']);

		// if the user is administrator of that course we show all the agenda items
		if ($array_course_info['status'] == '1')
		{
			//echo "course admin";
			$sqlquery = "SELECT
										DISTINCT a.*, i.*
										FROM ".$TABLEAGENDA." a,
											".$TABLE_ITEMPROPERTY." i
										WHERE a.id = i.ref  
										AND a.start_date>='".$start_filter."' AND a.start_date<='".$end_filter."'
										AND i.tool='".TOOL_CALENDAR_EVENT."'
										AND i.visibility='1'
										GROUP BY a.id
										ORDER BY a.start_date";
		}
		// if the user is not an administrator of that course
		else
		{
			//echo "GEEN course admin";
			if (is_array($group_memberships))
			{
				$sqlquery = "SELECT
													a.*, i.*
													FROM ".$TABLEAGENDA." a,
														 ".$TABLE_ITEMPROPERTY." i
													WHERE a.id = i.`ref` 
													AND a.start_date>='".$start_filter."' AND a.start_date<='".$end_filter."'
													AND i.tool='".TOOL_CALENDAR_EVENT."'
													AND	( i.to_user_id='".$_user['user_id']."' OR i.to_group_id IN (0, ".implode(", ", $group_memberships).") )
													AND i.visibility='1'
													ORDER BY a.start_date";
			}
			else
			{
				$sqlquery = "SELECT
													a.*, i.*
													FROM ".$TABLEAGENDA." a,
														 ".$TABLE_ITEMPROPERTY." i
													WHERE a.id = i.ref 
													AND a.start_date>='".$start_filter."' AND a.start_date<='".$end_filter."'
													AND i.tool='".TOOL_CALENDAR_EVENT."'
													AND ( i.to_user_id='".$_user['user_id']."' OR i.to_group_id='0')
													AND i.visibility='1'
													ORDER BY a.start_date";
			}
		}
		//echo "<pre>".$sqlquery."</pre>";
		// $sqlquery = "SELECT * FROM $agendadb WHERE (DAYOFMONTH(day)>='$start_day' AND DAYOFMONTH(day)<='$end_day')
		//				AND (MONTH(day)>='$start_month' AND MONTH(day)<='$end_month')
		//				AND (YEAR(day)>='$start_year' AND YEAR(day)<='$end_year')";
		$result = api_sql_query($sqlquery, __FILE__, __LINE__);
		while ($item = mysql_fetch_array($result))
		{
			$agendaday = date("j",strtotime($item['start_date']));
			$time= date("H:i",strtotime($item['start_date']));
			$URL = $_configuration['root_web']."main/calendar/agenda.php?cidReq=".urlencode($array_course_info["code"])."&amp;day=$agendaday&amp;month=$month&amp;year=$year#$agendaday"; // RH  //Patrick Cool: to highlight the relevant agenda item
			$items[$agendaday][$item['start_time']] .= "<i>$time</i> <a href=\"$URL\" title=\"".$array_course_info["name"]."\">".$array_course_info["visual_code"]."</a>  ".$item['title']."<br />";
		}
	}
	// sorting by hour for every day
	$agendaitems = array ();
	while (list ($agendaday, $tmpitems) = each($items))
	{
		sort($tmpitems);
		while (list ($key, $val) = each($tmpitems))
		{
			$agendaitems[$agendaday] .= $val;
		}
	}
	//print_r($agendaitems);
	return $agendaitems;
}
/**
 * This function shows all the forms that are needed form adding /editing a new personal agenda item
 * when there is no $id passed in the function we are adding a new agenda item, if there is a $id
 * we are editing
 * attention: we have to check that the student is editing an item that belongs to him/her
 */
function show_new_item_form($id = "")
{
	global $year, $MonthsLong;
	global $tbl_personal_agenda;
	global $_user;
	// we construct the default time and date data (used if we are not editing a personal agenda item)
	$today = getdate();
	$day = $today['mday'];
	$month = $today['mon'];
	$year = $today['year'];
	$hours = $today['hours'];
	$minutes = $today['minutes'];
	// if an $id is passed to this function this means we are editing an item
	// we are loading the information here (we do this after everything else
	// to overwrite the default information)
	if ($id <> "")
	{
		$sql = "SELECT * FROM ".$tbl_personal_agenda." WHERE user='".$_user['user_id']."' AND id='".$id."'";
		$result = api_sql_query($sql, __FILE__, __LINE__);
		$aantal = mysql_num_rows($result);
		if ($aantal <> 0)
		{
			$row = mysql_fetch_array($result);
			$year = substr($row['date'], 0, 4);
			$month = substr($row['date'], 5, 2);
			$day = substr($row['date'], 8, 2);
			$hours = substr($row['date'], 11, 2);
			$minutes = substr($row['date'], 14, 2);
			$title = $row['title'];
			$text = $row['text'];
		}
		else
		{
			return false;
		}
	}
	echo "<form method=\"post\" action=\"myagenda.php?action=add_personal_agenda_item&amp;id=$id\" name=\"newedit_form\">\n";
	echo "<table width=\"100%\" id=\"newedit_form\">\n";
	echo "\t<tr class=\"title\">\n\t\t<td colspan=\"3\"><h4>";
	echo ($_GET['action'] == 'edit_personal_agenda_item') ? get_lang("ModifyPersonalCalendarItem") : get_lang("AddPersonalCalendarItem");
	echo "</h4></td>\n\t</tr>\n";
	echo "\t<tr class=\"subtitle\">\n\t\t<td>\n";
	echo "<!-- date: 1 -> 31 -->\n";
	echo "\t\t".get_lang("Date").": \n";
	// ********** The form containing the days (0->31) ********** \\
	echo "<select name=\"frm_day\">\n";
	// small loop for filling all the dates
	// 2do: the available dates should be those of the selected month => february is from 1 to 28 (or 29) and not to 31
	for ($i = 1; $i <= 31; $i ++)
	{
		// values have to have double digits
		if ($i <= 9)
		{
			$value = "0".$i;
		}
		else
		{
			$value = $i;
		}
		// the current day is indicated with [] around the date
		if ($value == $day)
		{
			echo "\t\t\t\t <option value=\"".$value."\" selected>".$i."</option>\n";
		}
		else
		{
			echo "\t\t\t\t <option value=\"".$value."\">".$i."</option>\n";
		}
	}
	echo "</select>\n\n";
	// ********** The form containing the months (jan->dec) ********** \\
	echo "<!-- month: january -> december -->\n";
	echo "<select name=\"frm_month\">\n";
	for ($i = 1; $i <= 12; $i ++)
	{
		// values have to have double digits
		if ($i <= 9)
		{
			$value = "0".$i;
		}
		else
		{
			$value = $i;
		}
		// the current month is indicated with [] around the month name
		if ($value == $month)
		{
			echo "\t<option value=\"".$value."\" selected>".$MonthsLong[$i -1]."</option>\n";
		}
		else
		{
			echo "\t<option value=\"".$value."\">".$MonthsLong[$i -1]."</option>\n";
		}
	}
	echo "</select>\n\n";
	// ********** The form containing the years ********** \\
	echo "<!-- year -->\n";
	echo "<select name=\"frm_year\">";
	echo "<option value=\"". ($year -1)."\">". ($year -1)."</option>\n";
	echo "<option value=\"".$year."\" selected>".$year."</option>\n";
	for ($i = 1; $i <= 5; $i ++)
	{
		$value = $year + $i;
		echo "\t<option value=\"".$value."\">".$value."</option>\n";
	}
	echo "</select>";
	echo "<a title=\"Kalender\" href=\"javascript:openCalendar('newedit_form', 'frm_')\"><img src=\"../img/calendar_select.gif\" border=\"0\" valign=\"absmiddle\"/></a>";
	echo "</td><td width=\"50%\">";
	// ********** The form containing the hours  (00->23) ********** \\
	echo "<!-- time: hour -->\n";
	echo get_lang("Time").": \n";
	echo "<select name=\"frm_hour\">\n";
	for ($i = 1; $i <= 24; $i ++)
	{
		// values have to have double digits
		if ($i <= 9)
		{
			$value = "0".$i;
		}
		else
		{
			$value = $i;
		}
		// the current hour is indicated with [] around the hour
		if ($hours == $value)
		{
			echo "\t\t\t\t<option value=\"".$value."\" selected> ".$value." </option>\n";
		}
		else
		{
			echo "\t\t\t\t<option value=\"".$value."\"> ".$value." </option>\n";
		}
	}
	echo "</select>";
	// ********** The form containing the minutes ********** \\
	echo "<select name=\"frm_minute\">";
	echo "<option value=\"".$minutes."\">".$minutes."</option>";
	echo "<option value=\"00\">00</option>";
	echo "<option value=\"05\">05</option>";
	echo "<option value=\"10\">10</option>";
	echo "<option value=\"15\">15</option>";
	echo "<option value=\"20\">20</option>";
	echo "<option value=\"25\">25</option>";
	echo "<option value=\"30\">30</option>";
	echo "<option value=\"35\">35</option>";
	echo "<option value=\"40\">40</option>";
	echo "<option value=\"45\">45</option>";
	echo "<option value=\"50\">50</option>";
	echo "<option value=\"55\">55</option>";
	echo "</select>";
	echo "</td></tr>";
	// ********** The title field ********** \\
	echo "<tr class=\"subtitle\"><td colspan=\"2\">";
	echo get_lang('Title').': <input type="text" name="frm_title" size="50" value="'.$title.'" />';
	echo "</td></tr>";
	// ********** The text field ********** \\
	echo "<tr><td colspan=\"2\">";
	//api_disp_html_area('frm_content', $text, '300px');
	echo'<textarea name="frm_content" style="width: 450px; height: 100px;">'.$text.'</textarea>';
	echo "</td></tr>";
	// ********** The Submit button********** \\
	echo "<tr><td colspan=\"2\">";
	echo '<input type="submit" name="Submit" value="'.get_lang('Ok').'" />';
	echo "</td></tr>";
	echo "</table>\n</form>\n";
}
/**
 * This function shows all the forms that are needed form adding a new personal agenda item
 */
function store_personal_item($day, $month, $year, $hour, $minute, $title, $content, $id = "")
{
	global $tbl_personal_agenda;
	global $_user;
	//constructing the date
	$date = $year."-".$month."-".$day." ".$hour.":".$minute.":00";
	if ($id <> "")
	{ // we are updating
		$sql = "UPDATE ".$tbl_personal_agenda." SET user='".$_user['user_id']."', title='".$title."', text='".$content."', date='".$date."' WHERE id='".$id."'";
	}
	else
	{ // we are adding a new item
		$sql = "INSERT INTO $tbl_personal_agenda (user, title, text, date) VALUES ('".$_user['user_id']."','$title', '$content', '$date')";
	}
	$result = api_sql_query($sql, __FILE__, __LINE__);
}
/**
 * This function finds all the courses of the user and returns an array containing the
 * database name of the courses.
 */
function get_courses_of_user()
{
	global $TABLECOURS;
	global $TABLECOURSUSER;
	global $_user;
	$sql_select_courses = "SELECT course.code k, course.visual_code  vc,
									course.title i, course.tutor_name t, course.db_name db, course.directory dir, course_rel_user.status status
			                        FROM    $TABLECOURS       course,
											$TABLECOURSUSER   course_rel_user
			                        WHERE course.code = course_rel_user.course_code
			                        AND   course_rel_user.user_id = '".$_user['user_id']."'";
	$result = api_sql_query($sql_select_courses);
	while ($row = mysql_fetch_array($result))
	{
		// we only need the database name of the course
		$courses[] = array ("db" => $row['db'], "code" => $row['k'], "visual_code" => $row['vc'], "title" => $row['i'], "directory" => $row['dir'], "status" => $row['status']);
	}
	return $courses;
}
/**
 * This function retrieves all the personal agenda items and add them to the agenda items found by the other functions.
 */
function get_personal_agenda_items($agendaitems, $day = "", $month = "", $year = "", $week = "", $type)
{
	global $tbl_personal_agenda;
	global $_user;
	global $_configuration;
	// 1. creating the SQL statement for getting the personal agenda items in MONTH view
	if ($type == "month_view" or $type == "") // we are in month view
	{
		$sql = "SELECT * FROM ".$tbl_personal_agenda." WHERE user='".$_user['user_id']."' and MONTH(date)='".$month."' AND YEAR(date) = '".$year."'  ORDER BY date ASC";
	}
	// 2. creating the SQL statement for getting the personal agenda items in WEEK view
	if ($type == "week_view") // we are in week view
	{
		$start_end_day_of_week = calculate_start_end_of_week($week, $year);
		$start_day = $start_end_day_of_week['start']['day'];
		$start_month = $start_end_day_of_week['start']['month'];
		$start_year = $start_end_day_of_week['start']['year'];
		$end_day = $start_end_day_of_week['end']['day'];
		$end_month = $start_end_day_of_week['end']['month'];
		$end_year = $start_end_day_of_week['end']['year'];
		// in sql statements you have to use year-month-day for date calculations
		$start_filter = $start_year."-".$start_month."-".$start_day." 00:00:00";
		$end_filter = $end_year."-".$end_month."-".$end_day." 23:59:59";
		$sql = " SELECT * FROM ".$tbl_personal_agenda." WHERE user='".$_user['user_id']."'
								AND date>='".$start_filter."' AND date<='".$end_filter."'";
	}
	// 3. creating the SQL statement for getting the personal agenda items in DAY view
	if ($type == "day_view") // we are in day view
	{
		// we could use mysql date() function but this is only available from 4.1 and higher
		$start_filter = $year."-".$month."-".$day." 00:00:00";
		$end_filter = $year."-".$month."-".$day." 23:59:59";
		$sql = " SELECT * FROM ".$tbl_personal_agenda." WHERE user='".$_user['user_id']."' AND date>='".$start_filter."' AND date<='".$end_filter."'";
	}
	//echo "day:".$day."/";
	//echo "month:".$month."/";
	//echo "year:".$year."/";
	//echo "week:".$week."/";
	//echo $type."<p>";
	//echo "<pre>".$sql."</pre>";
	$result = api_sql_query($sql, __FILE__, __LINE__);
	while ($item = mysql_fetch_array($result))
	{
		// we break the date field in the database into a date and a time part
		$agenda_db_date = explode(" ", $item[date]);
		$date = $agenda_db_date[0];
		$time = $agenda_db_date[1];
		// we divide the date part into a day, a month and a year
		$agendadate = explode("-", $date);
		$year = intval($agendadate[0]);
		$month = intval($agendadate[1]);
		$day = intval($agendadate[2]);
		// we divide the time part into hour, minutes, seconds
		$agendatime = explode(":", $time);
		$hour = $agendatime[0];
		$minute = $agendatime[1];
		$second = $agendatime[2];
		// if the student has specified a course we a add a link to that course
		if ($item['course'] <> "")
		{
			$url = $_configuration['root_web']."main/calendar/agenda.php?cidReq=".urlencode($item['course'])."&amp;day=$day&amp;month=$month&amp;year=$year#$day"; // RH  //Patrick Cool: to highlight the relevant agenda item
			$course_link = "<a href=\"$url\" title=\"".$item['course']."\">".$item['course']."</a>";
		}
		else
		{
			$course_link = "";
		}
		// Creating the array that will be returned. If we have week or month view we have an array with the date as the key
		// if we have a day_view we use a half hour as index => key 33 = 16h30
		if ($type !== "day_view") // This is the array construction for the WEEK or MONTH view
		{
			$agendaitems[$day] .= "<div><i>$hour:$minute</i> $course_link  <a href=\"myagenda.php?action=view&amp;view=personal&amp;day=$day&amp;month=$month&amp;year=$year&amp;id=".$item['id']."#".$item['id']."\" class=\"personal_agenda\">".$item['title']."</a></div>";
		}
		else // this is the array construction for the DAY view
			{
			$halfhour = 2 * $agendatime['0'];
			if ($agendatime['1'] >= '30')
			{
				$halfhour = $halfhour +1;
			}
			$agendaitems[$halfhour] .= "<div><i>$hour:$minute</i> $course_link  <a href=\"myagenda.php?action=view&amp;view=personal&amp;day=$day&amp;month=$month&amp;year=$year&amp;id=".$item['id']."#".$item['id']."\" class=\"personal_agenda\">".$item['title']."</a></div>";
		}
	}
	//print_r($agendaitems);
	return $agendaitems;
}
/**
 * This function retrieves one personal agenda item returns it.
 * @param	int	The agenda item ID
 * @return 	array	The results of the database query, or null if not found
 */
function get_personal_agenda_item($id)
{
	$tbl_personal_agenda = Database :: get_user_personal_table(TABLE_PERSONAL_AGENDA);
	$id = Database::escape_string($id);
	// make sure events of the personal agenda can only be seen by the user himself
	$user = api_get_user_id();
	$sql = " SELECT * FROM ".$tbl_personal_agenda." WHERE id=".$id." AND user = ".$user;
	$result = api_sql_query($sql, __FILE__, __LINE__);
	if(Database::num_rows($result)==1)
	{
		$item = Database::fetch_array($result);
	}
	else
	{
		$item = null;
	}
	return $item;
}
/**
 * This function retrieves all the personal agenda items of the user and shows
 * these items in one list (ordered by date and grouped by month (the month_bar)
 */
function show_personal_agenda()
{
	global $tbl_personal_agenda;
	global $MonthsLong, $charset;
	global $_user;
	// The SQL statement that retrieves all the personal agenda items of this user
	$sql = "SELECT * FROM ".$tbl_personal_agenda." WHERE user='".$_user['user_id']."' ORDER BY date DESC";
	$result = api_sql_query($sql, __FILE__, __LINE__);
	// variable initialisation
	$month_bar = "";
	// starting the table output
	echo "<table id=\"agenda_list\">\n";
	// setting the default day, month and year
	if (!$_GET['day'] AND !$_GET['month'] AND !$_GET['year'])
	{
		$today = getdate();
		$year = $today['year'];
		$month = $today['mon'];
		$day = $today['mday'];
	}
	$export_icon = api_get_path('WEB_IMG_PATH').'export.png';
	$export_icon_low = api_get_path('WEB_IMG_PATH').'export_low_fade.png';
	$export_icon_high = api_get_path('WEB_IMG_PATH').'export_high_fade.png';
	if (Database::num_rows($result) > 0)
	{
		while ($myrow = Database::fetch_array($result))
		{
			/*--------------------------------------------------
					display: the month bar
			  --------------------------------------------------*/
			if ($month_bar != date("m", strtotime($myrow["date"])).date("Y", strtotime($myrow["date"])))
			{
				$month_bar = date("m", strtotime($myrow["date"])).date("Y", strtotime($myrow["date"]));
				echo "<tr><td class=\"title\" colspan=\"2\" class=\"month\" valign=\"top\">".$MonthsLong[date("n", strtotime($myrow["date"])) - 1]." ".date("Y", strtotime($myrow["date"]))."</td></tr>\n";
			}
			// highlight: if a date in the small calendar is clicked we highlight the relevant items
			$db_date = (int) date("d", strtotime($myrow["date"])).date("n", strtotime($myrow["date"])).date("Y", strtotime($myrow["date"]));
			if ($_GET["day"].$_GET["month"].$_GET["year"] <> $db_date)
			{
				$style = "data";
				$text_style = "text";
			}
			else
			{
				$style = "datanow";
				$text_style = "text";
			}
			/*--------------------------------------------------
			 			display: date and time
			  --------------------------------------------------*/
			echo "\t<tr>\n\t\t";
			echo '<td class="'.$style.'">';
			// adding an internal anchor
			echo "<a name=\"".$myrow["id"]."\"></a>";
			echo date("d", strtotime($myrow["date"]))." ".$MonthsLong[date("n", strtotime($myrow["date"])) - 1]." ".date("Y", strtotime($myrow["date"]))."&nbsp;";
			echo ucfirst(strftime(get_lang("timeNoSecFormat"), strtotime($myrow["date"])));
			echo "</td>";
			echo '<td class="'.$style.'">';
			echo '<a class="ical_export" href="ical_export.php?type=personal&id='.$myrow['id'].'&class=confidential" title="'.get_lang('ExportiCalConfidential').'"><img src="'.$export_icon_high.'" alt="'.get_lang('ExportiCalConfidential').'"/></a>';
			echo '<a class="ical_export" href="ical_export.php?type=personal&id='.$myrow['id'].'&class=private" title="'.get_lang('ExportiCalPrivate').'"><img src="'.$export_icon_low.'" alt="'.get_lang('ExportiCalPrivate').'"/></a>';
			echo '<a class="ical_export" href="ical_export.php?type=personal&id='.$myrow['id'].'&class=public" title="'.get_lang('ExportiCalPublic').'"><img src="'.$export_icon.'" alt="'.get_lang('ExportiCalPublic').'"/></a>';
			echo "\n\t\t</td>\n\t";
			echo "</tr>";
			/*--------------------------------------------------
			 			display: the title
			  --------------------------------------------------*/
			echo "<tr>";
			echo '<td class="'.$style.'" colspan="2">';
			echo $myrow['title'];
			echo "\n\t\t</td>\n\t";
			echo "</tr>\n";
			/*--------------------------------------------------
			 			display: the content
			  --------------------------------------------------*/
			$content = $myrow['text'];
			$content = make_clickable($content);
			$content = text_filter($content);
			echo "\t<tr>\n\t\t<td class=\"".$text_style."\" colspan='2'>";
			echo $content;
			echo "</td></tr>";
			/*--------------------------------------------------
			 			display: the edit / delete icons
			  --------------------------------------------------*/
			echo "\t<tr>\n\t\t<td class=\"".$text_style."\" colspan='2'>";
			echo "<a href=\"myagenda.php?action=edit_personal_agenda_item&amp;id=".$myrow['id']."\"><img src=\"../img/edit.gif\" border=\"0\" alt=\"".get_lang('Edit')."\" /></a>";
			echo "<a href=\"".api_get_self()."?action=delete&amp;id=".$myrow['id']."\" onclick=\"javascript:if(!confirm('".addslashes(htmlentities(get_lang('ConfirmYourChoice'),ENT_QUOTES,$charset))."')) return false;\"><img src=\"../img/delete.gif\" border=\"0\" alt=\"".get_lang('Delete')."\" /></a>";
			echo "</td></tr>";
		}
	}
	else
	{
		echo '<tr><td colspan="2">'.get_lang('NoAgendaItems').'</td></tr>';
	}
	echo "</table>\n";
}
/**
 * This function deletes a personal agenda item
 * There is an additional check to make sure that one cannot delete an item that
 * does not belong to him/her
 */
function delete_personal_agenda($id)
{
	global $tbl_personal_agenda;
	global $_user;
	if ($id <> '')
	{
		$sql = "SELECT * FROM ".$tbl_personal_agenda." WHERE user='".$_user['user_id']."' AND id='".$id."'";
		$result = api_sql_query($sql, __FILE__, __LINE__);
		$aantal = mysql_num_rows($result);
		if ($aantal <> 0)
		{
			$sql = "DELETE FROM ".$tbl_personal_agenda." WHERE user='".$_user['user_id']."' AND id='".$id."'";
			$result = api_sql_query($sql, __FILE__, __LINE__);
		}
	}
}
/**
 * This function calculates the startdate of the week (monday)
 * and the enddate of the week (sunday)
 * and returns it as an array
 */
function calculate_start_end_of_week($week_number, $year)
{
	// determine the start and end date
	// step 1: we calculate a timestamp for a day in this week
	$random_day_in_week = mktime(0, 0, 0, 1, 1, $year) + ($week_number-1) * (7 * 24 * 60 * 60); // we calculate a random day in this week
	// step 2: we which day this is (0=sunday, 1=monday, ...)
	$number_day_in_week = date('w', $random_day_in_week);
	// step 3: we calculate the timestamp of the monday of the week we are in
	$start_timestamp = $random_day_in_week - (($number_day_in_week -1) * 24 * 60 * 60);
	// step 4: we calculate the timestamp of the sunday of the week we are in
	$end_timestamp = $random_day_in_week + ((7 - $number_day_in_week +1) * 24 * 60 * 60) - 3600;
	// step 5: calculating the start_day, end_day, start_month, end_month, start_year, end_year
	$start_day = date('j', $start_timestamp);
	$start_month = date('n', $start_timestamp);
	$start_year = date('Y', $start_timestamp);
	$end_day = date('j', $end_timestamp);
	$end_month = date('n', $end_timestamp);
	$end_year = date('Y', $end_timestamp);
	$start_end_array['start']['day'] = $start_day;
	$start_end_array['start']['month'] = $start_month;
	$start_end_array['start']['year'] = $start_year;
	$start_end_array['end']['day'] = $end_day;
	$start_end_array['end']['month'] = $end_month;
	$start_end_array['end']['year'] = $end_year;
	return $start_end_array;
}
?>