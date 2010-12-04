<?php
require_once './include/autoload.php';
try {
	$site = new Site('Account Recovery');

	if ($site->user->verifyUser()) {
		new HTML_Text($site->content,'You are logged in.');
	} else {
		new RecoverAccount($site->content,$site->infoMsg,$site->siteInfo,$site->user);
	}

	$site->printPage();
} catch (Exception $err) { echo $err; }
?>
