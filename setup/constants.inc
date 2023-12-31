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
 * $Id: constants.inc 17848 2008-08-13 13:31:49Z JensT $
 */

insertFormJS('config');
?>
<input type="hidden" name="this_page" value="constants">
<input type="hidden" name="next_page" value="defaults">
<input type="hidden" name="back_page" value="check">

<div class="header"><?php printf (gTranslate('config',"Gallery Configuration Wizard: Step %d"), 2); ?></div>

<div class="sitedesc">
<?php
echo gTranslate('config',"In this step you'll customize various settings for Gallery.  If this is the first time you've customized Gallery, you'll notice that we've tried to pick reasonable defaults for you.  Even so, you'll probably have to make at least some minor changes.  If you've already configured Gallery before, then you'll notice that this page already has your current settings loaded, so you probably won't need to do much.");
echo '<br>';
echo gTranslate('config',"Switch through the corresponding tab and fill the values you need. Some, but not all values are required. If you have finished this step use the button to go to the next Step.");
?>
</div>

<p align="center">
	<input type="submit" name="go_back" disabled="disabled" value="&lt;-- <?php echo gTranslate('config',"Previous Step") ?>">
        <input type="button" name="go_backTab" disabled="disabled" value="&lt;- <?php echo gTranslate('config',"Previous Tab") ?>" onClick="section_tabs.prevTab()">
        <input type="button" name="go_nextTab" disabled="disabled" value="<?php echo gTranslate('config',"Next Tab") ?> -&gt;" onClick="section_tabs.nextTab()">
        <input type="submit" name="go_next" disabled="disabled" value="<?php echo gTranslate('config',"Next Step") ?> --&gt;">
        <input type="submit" name="go_defaults" disabled="disabled" value="<?php echo gTranslate('config',"Use Defaults") ?>">
</p>
<?php

$initialtab = makeSectionTabs($constants);
echo '<input name="initialtab" id="initialtab" type="hidden" value="'. $initialtab .'">';
echo "<div style=\"clear: both\"></div>";

?>
<script type="text/javascript" language="Javascript">

function toggleGraphics(show) {
	if(show == 'ImageMagick') {
		document.getElementById('subgroup_ImageMagick').style.display = 'inline';
		document.getElementById('subgroup_Netpbm').style.display      = 'none';
	}
	else {
		document.getElementById('subgroup_ImageMagick').style.display = 'none';
		document.getElementById('subgroup_Netpbm').style.display      = 'inline';
	}
}

function toggleSMTP(show) {
	if(show == 'yes') {
		document.getElementById('subgroup_SMTP').style.display = 'inline';
	}
	else {
		document.getElementById('subgroup_SMTP').style.display = 'none';
	}
}
</script>
<?php
$i = 0;

foreach ($constants as $key => $val) {
	if( isset($val["enabled"]) && $val["enabled"] == "no") {
		continue;
	}

	if (isset($val["type"]) && ($val["type"] === "block_start" )) {
		echo "\n<table width=\"100%\" class=\"inner\">";
		echo evenOdd_row(make_fields($key, $val), $i % 2);
		continue;
	}

	if (isset($val["type"]) && ($val["type"] === 'subgroup' )) {
		echo '<div id="'. $key .'" style="display: '. $val['display'] .'">';
		echo '<center><div class="subgroup">'. $val['title'] .'</div></center>';
		if (isset($val['desc'])) {
			echo '<div class="inner desc">'. $val['desc'] .'</div>';
		}
		continue;
	}

	if (isset($val["type"]) && ($val["type"] === 'subgroup_end' )) {
		echo '</div>';
		continue;
	}

	if (isset($val["type"]) && ($val["type"] === "block_end" )) {
		echo evenOdd_row(make_fields($key, $val), $i++ % 2);
		echo "\n<tr><td colspan=\"2\"> &nbsp; </td></tr>";
		echo "\n</table>";
		continue;
	}

	if (isset($val["type"]) && ($val["type"] === 'group_start' )) {
		echo "\n<div id=\"". $val["name"] ."\" style=\"display: ". $val["default"] ."\">";
		echo make_separator($key, $val);
		continue;

	}

	if (isset($val["type"]) && $val["type"] === 'group_end') {
		echo "\n</div>";
		continue;
	}

	$onThisPage[$key] = 1;
	$preserve[$key] = 1;

	/* if the variable is hidden, lock it in as we don't want to use previous values*/
	if (isset($val["type"]) && ($val["type"] === "hidden")) {
		if (!empty($val["persistent"]) && isset($gallery->app->$key)) {
			$val["value"] = $gallery->app->$key;
		}
		elseif (is_array($val["value"])) {
			$key = $key."[]";
			$val["value"] = $val["value"][0];
		}

		if (ereg("^(..*)\[.*\]$", $key, $matches)) {
			unset($preserve[$key]);
			$preserve[$matches[1]] = 1;
			unset($onThisPage[$key]);
			$onThisPage[$matches[1]] = 1;
		}
		list($f1, $f2) = make_fields($key, $val);
		echo $f2;
	}
	else {
		echo "\n<table width=\"100%\" class=\"inner\">";
		/* Support $app-> for backwards compatibility */
		if (isset($app->$key)) {
			$val["value"] = $app->$key;
		}

		/* $gallery->app-> is the desired container, however */
		if (isset($gallery->app->$key)) {
			$val["value"] = $gallery->app->$key;
		}

		if (!isset($go_defaults)) {
			if (ereg("^(..*)\[.*\]$", $key, $matches)) {
				if (isset ($gallery->session->configForm->$key)) {
					$val['value'] = $gallery->session->configForm->$key;
				}

				unset($preserve[$key]);
				$preserve[$matches[1]] = 1;
				unset($onThisPage[$key]);
				$onThisPage[$matches[1]] = 1;
			}
			else if(isset($gallery->session->configForm->$key)) {
				$val["value"] = $gallery->session->configForm->$key;

			}
			if (get_magic_quotes_gpc() && !isset($val['noStripSlash'])) {
				if (is_array($val["value"])) {
					$val["value"] = array_map("stripslashes", $val["value"]);
				} else {
					$val["value"] = stripslashes($val["value"]);
				}
			}
		}

		// Protect quote characters to avoid screwing up HTML forms
		if (isset($val['value'])) {
			$val['value'] = str_replace('"', '&quot;', $val['value']);
		}
		else {
			$val['value'] = '';
		}

		echo evenOdd_row(make_fields($key, $val), $i++ % 2);
	}

	if (isset($val['type']) && ($val['type'] === 'hidden')) {
	} else {
		echo "\n</table>";
	}
}
?>

<p align="center">
	<input type="submit" name="go_back" disabled="disabled" value="&lt;-- <?php echo gTranslate('config',"Previous Step") ?>">
        <input type="button" name="go_backTab" disabled="disabled" value="&lt;- <?php echo gTranslate('config',"Previous Tab") ?>" onClick="section_tabs.prevTab()">
        <input type="button" name="go_nextTab" disabled="disabled" value="<?php echo gTranslate('config',"Next Tab") ?> -&gt;" onClick="section_tabs.nextTab()">
        <input type="submit" name="go_next" disabled="disabled" value="<?php echo gTranslate('config',"Next Step") ?> --&gt;">
        <input type="submit" name="go_defaults" disabled="disabled" value="<?php echo gTranslate('config',"Use Defaults") ?>">
</p>
