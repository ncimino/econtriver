<?php
require_once './include/autoload.php';
try {
	$site = new Site('Profile');

	if (!$site->user->verifyUser()) {
		new HTML_Text($site->content,'You must be logged in to manage your account.');
	} else {
		new ManageProfile($site->content,$site->infoMsg,$site->siteInfo,$site->user,$site->body);
	}

	$site->printPage();
} catch (Exception $err) { echo $err; }
?>
