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
 * $Id: UserDB.php 17801 2008-08-05 23:10:49Z JensT $
 */

class Abstract_UserDB {

	/* By default, UserDB can't create a user */
	function canCreateUser() {
		return false;
	}

	/* By default, UserDB can't modify a user */
	function canModifyUser() {
		return false;
	}

	/* By default, UserDB can't delete a user */
	function canDeleteUser() {
		return false;
	}

	function save() {
		return false;
	}

	function getNobody() {
		return $this->nobody;
	}

	function getEverybody() {
		return $this->everybody;
	}

	function getLoggedIn() {
		return $this->loggedIn;
	}

	function getUidList() {
		print "Error: getUidList() should be overridden by a subclass!";
	}

	function getUserByUsername($username, $level=0) {
		print "Error: getUserByUsername() should be overridden by a subclass!";
	}

	function getUserByUid($uid) {
		print "Error: getUserByUid() should be overridden by a subclass!";
	}
	function versionOutOfDate() {
		return false;
	}
 	function integrityCheck() {
		return 0;
	}

	/*
	 * No conversion is necessary for most user database formats.
	 */
	function convertUidToNewFormat($uid) {
		return $uid;
	}

	/**
	 * Returns whether the UserDB was succesfully initialized or not.
	 * Is currently only used for standalone Gallery UserDB.
	 *
	 * @return boolean	 true if succesfully initialized.
	 * @author Jens Tkotz
	 */
	function isInitialized() {
		return true;
	}
}
?>
