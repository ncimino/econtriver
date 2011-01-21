<?php
class QA_Frame extends Frame {
	
	function __construct($parentElement,$title,$tabStartIndex) {
		parent::__construct($parentElement,$title,QA_Module::I_FS,QA_Module::C_MGMT,$tabStartIndex);
		$this->build();
	}

}
?>