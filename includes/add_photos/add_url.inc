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
 * $Id: add_url.inc 17827 2008-08-07 23:25:34Z JensT $
 */

echo "\n<p>";
echo gTranslate('core', "Upload any images found at this location.") . ' ';
echo gTranslate('core', "The location can either be a URL or a local directory on the server.");
echo "\n<p>\n";

echo '<b>'. gTranslate('core', "Tip:") . '</b> ';
echo gTranslate('core', "FTP images to a directory on your server then provide that path here!");

echo makeFormIntro("save_photos.php",
array('name' => 'uploadurl_form'),
array('type' => 'popup'));
?>
<input type="text" name="urls[]" size="40">
<p>
<?php
echo gTranslate('core', "What would you like the captions to default to?");
echo "\n<br><br>";
include(dirname(__FILE__) .'/captionOptions.inc.php');
?>
</p>
<?php
if (isset($gallery->app->watermarkDir)) {
	global $watermarkForm;

	$watermarkForm['askRecursive']	= 0;
	$watermarkForm['askPreview']	= 0;
	$watermarkForm['allowNone']		= 1;
	includeLayout('watermarkform.inc');
}
?>
<p align="center">
<input class="g-button" type="submit" value="<?php echo gTranslate('core', "Submit URL or directory") ?>" onClick='parent.opener.showProgress(); document.uploadurl_form.submit()'>
<?php echo gButton('close', gTranslate('core', "Cancel"), 'parent.close()'); ?>
</p>
</form>
