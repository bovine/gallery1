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
 * $Id: LoggedInUser.php 17806 2008-08-06 16:16:29Z JensT $
 */

if (! class_exists('Abstract_User')) {
	exit;
}

class LoggedinUser extends Abstract_User {
	function __construct() {
		$this->username = "LOGGEDIN";
		$this->fullname = _("Anonymous User") ;
		$this->setIsAdmin(false);
		$this->setCanCreateAlbums(false);
		$this->uid = "loggedin";
	}

	function isLoggedIn() {
		return false;
	}

	function isPseudo() {
		return true;
	}
}

?>
