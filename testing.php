<?php
require_once './include/autoloader.php';

$DOMDoc = new DOMDocument();
echo $DOMDoc->getDocumentMode()."<br>";
echo $DOMDoc->getDomain()."<br>";
echo $DOMDoc->getReferrer()."<br>";
echo $DOMDoc->getTitle()."<br>";
echo $DOMDoc->getURL()."<br>";
?>