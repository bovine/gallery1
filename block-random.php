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
 * $Id: block-random.php 18468 2008-11-04 16:50:34Z JensT $
 */
/*
 * This block selects a random photo for display.  It will only display photos
 * from albums that are visible to the public.  It will not display hidden
 * photos.  
 *
 * Once a day (or whatever you set CACHE_EXPIRED to) we scan all albums and
 * create a cache file listing each public album and the number of photos it
 * contains.  For all subsequent attempts we use that cache file.  This means
 * that if you change your albums around it may take a day before this block
 * starts (or stops) displaying them.
 *
 * If your Gallery is embedded and you call it via an URL, 
 * make sure you are giving the needed paramters.
 *
 * *Nuke:
 * http://<URL to your Nuke>/modules.php?op=modload&name=gallery&file=index&include=block-random.php
 *
 * Mambo / Joomla :
 * http://<URL to Mambo>/index.php?option=com_gallery&Itemid=XXX
 */

// Random block does not require authentication of any sort... don't use sessions
$GALLERY_NO_SESSIONS = 1;
require(dirname(__FILE__) . "/init.php");

define('CACHE_FILE', $gallery->app->albumDir . "/block-random.dat");
define('CACHE_EXPIRED', $gallery->app->blockRandomCache);

// Check the cache file to see if it's up to date
$rebuild = 1;
if (fs_file_exists(CACHE_FILE)) {
	$stat = fs_stat(CACHE_FILE);
	$mtime = $stat[9];
	if ((time() - $mtime) < CACHE_EXPIRED) {
		$rebuild = 0;
	}
}

if ($rebuild) {
	scanAlbums();
	saveGalleryBlockRandomCache();
}
else {
	readGalleryBlockRandomCache();
}

$i = 0;
do { 
	$success = doPhoto();
	$i++;
} while (empty($success) && $i < $gallery->app->blockRandomAttempts);

if (empty($success)) {
	echo gTranslate('core', "No photo chosen.");
}

function doPhoto() {
	$album = chooseAlbum();

	if (!empty($album)) {
		$index = choosePhoto($album);
	}

	if (!empty($index)) {
		$id = $album->getPhotoId($index);
		$caption = $album->getCaption($index) ? '<br>'. $album->getCaption($index) : '';
		$photoUrl = makeAlbumUrl($album->fields['name'], $id);
		$imageUrl = $album->getThumbnailTag($index);
		$albumUrl = makeAlbumUrl($album->fields['name']);
		$albumTitle = $album->fields['title'];
?>
  <div class="random-block">
    <div class="random-block-photo">
    <a href="<?php echo $photoUrl; ?>"><?php echo $imageUrl; ?></a>
    <?php echo $caption; ?>
    
    </div>
    <?php printf (gTranslate('core',"From album: %s"), "<a href=\"$albumUrl\">$albumTitle</a>"); ?>
  </div>
<?php
		return 1;
	} else {
		return 0;
	}
}

/*
 * --------------------------------------------------
 * Support functions
 * --------------------------------------------------
 */

function saveGalleryBlockRandomCache() {
	global $cache;
	safe_serialize($cache, CACHE_FILE);
}

function readGalleryBlockRandomCache() {
	global $cache;

	$sCache = fs_file_get_contents(CACHE_FILE);
	$cache = unserialize($sCache);
}

function choosePhoto($album) {
	global $cache;

	$count = $cache[$album->fields["name"]];
	if ($count == 0) {
		// Shouldn't happen
		return null;
	} elseif ($count == 1) {
		$choose = 1;
		if ($album->isAlbum($choose)) {
			return null;
		}
	} else {
		$choose = mt_rand(1, $count);
		$wrap = 0;
		while ($album->isHiddenRecurse($choose) || $album->isAlbum($choose)) {
			$choose++;
			if ($choose > $album->numPhotos(1)) {
				$choose = 1;
				$wrap++;
				if ($wrap == 2) {
					return null;
				}
			}
		}
	}

	return $choose;
}

function chooseAlbum() {
	global $cache;

	/*
	* The odds that an album will be selected is proportional
	* to the number of (visible) items in the album.
	*/
	$total = 0;
	foreach ($cache as $name => $count) {
		if (empty($choose)) {
			$choose = $name;
		}

		$total += $count;
		if ($total != 0 && ($total == 1 || mt_rand(1, $total) <= $count)) {
			$choose = $name;
		}
	}

	if ($choose) {
		$album = new Album();
		$album->load($choose);
		return $album;
	} else {
		return null;
	}
}

function scanAlbums() {
	global $cache;
	global $gallery;

	$cache = array();
	$everybody = $gallery->userDB->getEverybody();
	$albumDB = new AlbumDB();
	foreach ($albumDB->albumList as $tmpAlbum) {
		if ($tmpAlbum->canReadRecurse($everybody->getUid()) && !$tmpAlbum->isHiddenRecurse()) {
			$seeHidden = $everybody->canWriteToAlbum($tmpAlbum);
			$numPhotos = $tmpAlbum->numPhotos($seeHidden);
			$name = $tmpAlbum->fields["name"];
			if ($numPhotos > 0) {
				$cache[$name] = $numPhotos;
			}
		}
	}
}
?>
