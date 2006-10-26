<?php // $Id: install_files.inc.php 6030 2005-08-17 13:56:28Z bmol $
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, 44 rue des palais, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/
/**
==============================================================================
* Install the Dokeos files
* Notice : This script has to be included by install/index.php
*
* The script creates two files:
* - claro_main.conf.php, the file that contains very important info for Dokeos
*   such as the database names.
* - .htaccess file (in the courses directory) that is optional but improves
*   security
*
* @package dokeos.install
==============================================================================
*/

if(defined('DOKEOS_INSTALL'))
{
	// Write the Dokeos config file
	write_dokeos_config_file('../inc/conf/claro_main.conf.php');
	// Write a distribution file with the config as a backup for the admin
	write_dokeos_config_file('../inc/conf/claro_main.conf.dist.php');
	// Write a .htaccess file in the course repository
	write_courses_htaccess_file($urlAppendPath);
}
else
{
	echo 'You are not allowed here !';
}
?>