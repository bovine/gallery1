<?php
/*
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2008 Bharat Mediratta
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * $Id: Version.php 18732 2008-11-17 23:12:00Z JensT $
 */

$gallery = (object)[];
$gallery->version = '1.5.10';
$gallery->config_version = 97;
$gallery->album_version = 38;
$gallery->user_version = 6;
$gallery->url = "http://gallery.sourceforge.net";

/* do not edit the date!! modified by SVN */
$gallery->last_change = strtotime(substr('$Date: 2008-11-17 18:12:00 -0500 (Mon, 17 Nov 2008) $', 7, -20));
/*
 * PostNuke version info
 */
$modversion['name'] = 'Gallery'; // Module name
$modversion['version'] = $gallery->version; // Version Number
$modversion['description'] = 'Photo and Movie gallery'; // Module Description
$modversion['credits'] = 'AUTHORS'; // Credits File
$modversion['help'] = 'README'; // Help File
$modversion['changelog'] = 'ChangeLog'; //Change Log File
$modversion['license'] = 'LICENSE.txt'; // License File
$modversion['official'] = 0; // Official PostNuke Approved Module? 1 = yes, 0 = no
$modversion['author'] = 'Bharat Mediratta'; // Author
$modversion['contact'] = $gallery->url; // The Authors Website or Contact Email Address
$modversion['admin'] = 0; // Leave at 0
if (!isset($modname) && isset($name)) {
    $modname = $name;
} else {
    $modname = '';
}
$modversion['securityschema'] = array("$modname::" => '::'); // Permission Component
?>
