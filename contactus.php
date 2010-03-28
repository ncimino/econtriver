<?php
require_once './include/autoload.php';
$site = new Site('Contact Us');
new ContactUs($site->content,$site->infoMsg,$site->siteInfo,$site->user);
$site->printPage();
?>
