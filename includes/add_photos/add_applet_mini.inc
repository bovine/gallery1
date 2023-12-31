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
 * $Id: add_applet_mini.inc 17808 2008-08-06 16:42:42Z JensT $
 */

/**
 * Writes the Gallery Remote Mini-Applet for adding items.
 *
 * @package Gallery_Remote
 * @subpackage Add_Items
 */

?>
<p><?php echo gTranslate('core', "Use the Gallery Remote Mini Applet to upload pictures. It will take a short while to download.") ?></p>
<p><?php echo gTranslate('core', "If you don't have the Java Plugin 1.4 or later, you can use one of the other methods.") ?></p>

<p>

<?php
	drawApplet(
		400,
		300,
		"com.gallery.GalleryRemote.GRAppletMini",
		$gallery->app->photoAlbumURL . "/java/GalleryRemoteAppletMini.jar," .
			$gallery->app->photoAlbumURL . "/java/GalleryRemoteHTTPClient.jar," .
			$gallery->app->photoAlbumURL . "/java/applet_img.jar",
		$gallery->album->fields["name"],
		array(),
		array("toSysOut" => "true"),
		dirname(__FILE__) . "/add_applet_mini.cfg",
		gTranslate('core', "Your browser doesn't support applets; you should use one of the other upload methods.")
	);
?>

</p>
