<?php

/**
 * Defines the navigation icons.
 *
 * @package Gallery
 *
 * $Id: navIcons.php 17806 2008-08-06 16:16:29Z JensT $
*/

if (!isset($gallery)) {
	exit;
}

// Path is relative to the icons folder
if ($gallery->direction == 'ltr') {
	$fpImg = 'navigation/nav_first.gif';
	$ppImg = 'navigation/nav_prev.gif';
	$npImg = 'navigation/nav_next.gif';
	$lpImg = 'navigation/nav_last.gif';
}
else {
	$fpImg = 'navigation/nav_last.gif';
	$ppImg = 'navigation/nav_next.gif';
	$npImg = 'navigation/nav_prev.gif';
	$lpImg = 'navigation/nav_first.gif';
}
?>