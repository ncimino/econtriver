<?php
require_once './include/autoload.php';
$site = new Site('Registration');
if ($site->user->verifyUser()) {
	new HTMLText($site->content,'You are already registered.');
} else {
	new Registration($site->content,$site->infoMsg,$site->siteInfo,$site->user);
}
$site->printPage();
?>
