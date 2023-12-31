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
 * $Id: login.php 17837 2008-08-11 23:27:40Z JensT $
 */

require_once(dirname(__FILE__) . '/init.php');
require_once(dirname(__FILE__) . '/classes/Logins.php');

list($username, $gallerypassword, $login, $reset_username, $forgot) =
	getRequestVar(array('username', 'gallerypassword', 'login', 'reset_username', 'forgot'));

list($g1_return, $cmd) = getRequestVar(array('g1_return', 'cmd'));

$username = gHtmlSafe($username);

$g1_return = unhtmlentities(urldecode($g1_return));

if(!isValidGalleryUrl($g1_return) || empty($g1_return)) {
	$g1_return = makeGalleryHeaderUrl();
}

$loginFailure = array();
$resetInfo = array();

if(!empty($cmd) && $cmd === 'logout') {
	gallery_syslog("Logout by ". $gallery->session->username ." from ". $_SERVER['REMOTE_ADDR']);
	$gallery->session->username = '';
	$gallery->session->language = '';
	destroyGallerySession();

	// Prevent the 'you have to be logged in' error message
	// when the user logs out of a protected album
	createGallerySession();
	$gallery->session->gRedirDone = true;

	header("Location: $g1_return");
}

if (!empty($username) && !empty($gallerypassword) && !empty($login)) {
	$userLogins = new Logins();
	$userLogins->load();

	$tmpUser = $gallery->userDB->getUserByUsername($username);
	if ($userLogins->userIslocked($username)) {
		$loginFailure[] = array(
			'type' => 'error',
			'text' => gTranslate('core', "This account is locked due too much wrong login attempts. Wait for automatic unlock, or contact an administrator.")
		);
	}
	elseif ($tmpUser && $tmpUser->isCorrectPassword($gallerypassword)) {

		// User is successfully logged in, regenerate a new
		// session ID to prevent session fixation attacks
		createGallerySession(true);

		// Perform the login
		$tmpUser->log("login");
		$tmpUser->save();
		$gallery->session->username = $username;
		gallery_syslog("Successful login for $username from " . $_SERVER['REMOTE_ADDR']);

		if ($tmpUser->getDefaultLanguage() != "") {
			$gallery->session->language = $tmpUser->getDefaultLanguage();
		}

		$userLogins->reset($username);
		$userLogins->save();

		if (!$gallery->session->offline) {
			header("Location: $g1_return");
		}
		else {
			echo '<span class="error">'. gTranslate('core', "SUCCEEDED") . '</span><p>';
			return;
		}
	}
	elseif($tmpUser) {
		$loginFailure[] = array(
			'type' => 'error',
			'text' => gTranslate('core', "Invalid username or password.")
		);

		$userLogins->addLoginTry($username);
		$userLogins->save();

		$gallerypassword = null;
		gallery_syslog("Failed login for $username from " . $_SERVER['REMOTE_ADDR']);
	}
	else {
		$loginFailure[] = array(
			'type' => 'error',
			'text' => gTranslate('core', "Invalid username or password.")
		);
		$gallerypassword = null;
		gallery_syslog("Failed login attempt with an invalid username from " . $_SERVER['REMOTE_ADDR']);

		$userLogins->addLoginTry($username);
		$userLogins->save();
}
}
elseif (!empty($login) && empty($forgot)) {
	$loginFailure[] = array(
		'type' => 'information',
		'text' => gTranslate('core', "Please enter username and password!")
	);
}
elseif (!empty($forgot) && empty($reset_username)) {
	$resetInfo[] = array(
		'type' => 'information',
		'text' => gTranslate('core', "Please enter <i>your</i> username.")
	);
}
elseif (!empty($forgot) && !empty($reset_username)) {
	$tmpUser = $gallery->userDB->getUserByUsername($reset_username);

	if ($tmpUser) {
		if (check_email($tmpUser->getEmail())) {
			if (gallery_mail(
				$tmpUser->email,
				gTranslate('core', "New password request"),
				sprintf(gTranslate('core', "Someone requested a new password for user %s from Gallery '%s' on %s. You can create a password by visiting the link below. If you didn't request a password, please ignore this mail. "), $reset_username, $gallery->app->galleryTitle, $gallery->app->photoAlbumURL) . "\n\n" .
				sprintf(gTranslate('core', "Click to reset your password: %s"),
				$tmpUser->genRecoverPasswordHash()) . "\n",
				sprintf(gTranslate('core', "New password request %s"), $reset_username)))
			{
				$tmpUser->log("new_password_request");
				$tmpUser->save();
			}
			else {
				$resetInfo[] = array(
					'type' => 'error',
					'text' => gTranslate('core', "Email could not be sent.") .
						"<br>"  .
						sprintf(gTranslate('core', "Please contact %s administrators for a new password."), $gallery->app->galleryTitle)
				);
			}
		}
	}

	if(empty($resetInfo) && empty($loginFailure)) {
		$resetInfo[] = array(
			'type' => 'information',
			'text' => sprintf(gTranslate('core', "If there is a valid email-address for this user, then an email has been sent to the address stored for %s.  Follow the instructions to change your password.  If you do not receive this email, please contact the Gallery administrators."), $reset_username)
		);
	}
}

$title = sprintf(gTranslate('core', "Login to %s"), $gallery->app->galleryTitle);

if (!$GALLERY_EMBEDDED_INSIDE) {
doctype();
?>
<html>
<head>
  <title><?php echo sprintf(gTranslate('core', "Login to %s"), $gallery->app->galleryTitle) ?></title>
  <?php
	common_header();
?>
</head>
<body>
<?php
}

includeHtmlWrap("gallery.header");

$breadcrumb['text'][]	= languageSelector();
?>

<div class="popuphead"><?php echo sprintf(gTranslate('core', "Login to %s"), $gallery->app->galleryTitle) ?></div>
<div class="popup" align="center">
	<?php echo gTranslate('core', "Logging in gives you greater permission to view, create, modify and delete albums.") ?>
</div>

<?php

includeLayout('breadcrumb.inc');

echo "\n<br>";

echo infoBox($loginFailure);
echo "\n<br>";

?>

<div class="g-loginpage popup" align="center">
<fieldset>
<legend class="g-emphasis"><?php echo gTranslate('common', "Login") ?></legend>
<?php
echo makeFormIntro('login.php', array('name' => 'loginForm'));
?>
 	<table>
<?php
	echo gInput('text', 'username', gTranslate('core', "Username"), true, $username,array('class' => 'g-form-text g-usernameInput'));

	echo gInput('password', 'gallerypassword', gTranslate('core', "Password"), true, null, array('class' => 'g-form-text g-passwordInput'));
?>
	</table>

	<p align="center">
	<?php echo gSubmit('login', gTranslate('core', "Login")); ?>
	<?php echo gButton('cancel', gTranslate('core', "Cancel"), "location.href='$g1_return'"); ?>
	</p>

	<?php echo gInput('hidden', 'g1_return', '', false, urlencode($g1_return)); ?>
	</form>
</fieldset>

	<?php
	if (isset($gallery->app->emailOn) && $gallery->app->emailOn == 'yes') {
	?>

<fieldset>
    <legend class="g-sectioncaption g-emphasis"><?php echo gTranslate('core', "Forgotten your password?") ?></legend>
					<?php
  echo makeFormIntro('login.php', array('name' => 'resetForm'));
	echo infoBox($resetInfo);

	echo gInput('text', 'reset_username', gTranslate('core', "Username"), false, $username, array('class' => 'g-form-text g-usernameInput'));
	echo "\n<p align=\"center\">";
	echo gSubmit('forgot', gTranslate('core', "Send me my password"));
	echo "\n</p>";
?>

	</form>
</fieldset>

	<?php } /* End if-email-on */

	if ($gallery->app->selfReg == 'yes') {
	?>
<fieldset>
    <legend class="g-sectioncaption g-emphasis"><?php echo gTranslate('core', "No account at all?"); ?></legend>
    <div class="center">
	<?php echo gButton('register', gTranslate('core', "Register a new account."), popup('register.php')); ?>
	</div>
</fieldset>
	<?php
	}
	?>

	<script language="javascript1.2" type="text/JavaScript">
	<!--
	// position cursor in top form field
	document.loginForm.username.focus();
	//-->
	</script>


</div>
<?php
	includeHtmlWrap("gallery.footer");
?>

</body>
</html>

