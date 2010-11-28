<?php
class ExceptionHandler {
	private $error;
	
	function __construct($error) {
		$this->error = $error;
		$this->printException();
	}
	
	function printException() { 
		echo "An error occurred.<br>\n";
		$siteInfo = new SiteInfo();
		if($siteInfo->getDisplayErrors()) {
			echo "-> Message returned: \"".$this->error->getMessage()."\"<br>\n";
			echo "-> In file: '".$this->error->getFile()."'<br>\n";
			echo "-> On line: ".$this->error->getLine()."<br>\n";
			echo "-> Stack trace: <pre>".$this->error->getTraceAsString()."</pre>";
			echo "-> Exception Object Dump: <pre>";
			print_r($this->error);
			echo "</pre>";
		}
	}
}
?>