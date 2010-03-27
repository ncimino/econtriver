<?php
class UserInputs {
	private $user;

	function __construct($user) {
		$this->user = $user;
		$this->user->clearIds();
	}

	function inputEmail ($inputParentElement,$labelParentElement=NULL,$label='Email:',$inputName=NULL,$value=NULL) {
		if (empty($labelParentElement)) { $labelParentElement = $inputParentElement; }
		if (empty($inputName)) { $inputName = $this->user->getEmailName(); }
		if (empty($value)) { $value = $this->user->getEmail(); }
		$inputEmail = new HTMLInputText($inputParentElement,$inputName,$value);
		$this->user->setEmailId($inputEmail->getAttribute('id'));
		new HTMLLabel($labelParentElement,$label,$inputEmail->getAttribute('id'));
	}

	function inputPassword ($inputParentElement,$labelParentElement,$label='Password:',$inputName=NULL,$value=NULL) {
		if (empty($inputName)) { $inputName = $this->user->getPasswordName(); }
		$inputPassword = new HTMLInputPassword($inputParentElement,$inputName,$value);
		$this->user->setPasswordId($inputPassword->getAttribute('id'));
		new HTMLLabel($labelParentElement,$label,$inputPassword->getAttribute('id'));
	}

	function inputVerPassword ($inputParentElement,$labelParentElement,$label='Verify Password:',$inputName=NULL,$value=NULL) {
		if (empty($inputName)) { $inputName = $this->user->getVerPasswordName(); }
		$inputVerPassword = new HTMLInputPassword($inputParentElement,$inputName,$value);
		$this->user->setVerPasswordId($inputVerPassword->getAttribute('id'));
		new HTMLLabel($labelParentElement,$label,$inputVerPassword->getAttribute('id'));
	}

	function inputHandle ($inputParentElement,$labelParentElement=NULL,$label='User Name:',$inputName=NULL,$value=NULL) {
		if (empty($labelParentElement)) { $labelParentElement = $inputParentElement; }
		if (empty($inputName)) { $inputName = $this->user->getHandleName(); }
		if (empty($value)) { $value = $this->user->getHandle(); }
		$inputHandle = new HTMLInputText($inputParentElement,$inputName,$value);
		$this->user->setHandleId($inputHandle->getAttribute('id'));
		new HTMLLabel($labelParentElement,$label,$inputHandle->getAttribute('id'));
	}

	function selectFormat ($inputParentElement,$labelParentElement,$label='Date Format:',$inputName=NULL,$value=NULL) {
		if (empty($inputName)) { $inputName = $this->user->getDateFormatName(); }
		if (empty($value)) { $value = $this->user->getDateFormat(); }
		$selectFormat = new HTMLSelect($inputParentElement,$inputName);
		$this->user->setDateFormatId($selectFormat->getAttribute('id'));
		new HTMLLabel($labelParentElement,$label,$selectFormat->getAttribute('id'));
		$format[0]['php'] = "Y-m-d";
		$format[0]['display'] = "YYYY-MM-DD";
		$format[1]['php'] = "m/d/Y";
		$format[1]['display'] = "MM/DD/YYYY";
		foreach ($format as $entry) {
			($entry['php']==$value) ? $selected = TRUE : $selected = FALSE;
			new HTMLOption($selectFormat,$entry['display'],$entry['php'],$selected);
		}
	}

	function selectTimezone ($inputParentElement,$labelParentElement,$label='Timezone:',$inputName=NULL,$value=NULL) {
		if (empty($inputName)) { $inputName = $this->user->getTimezoneName(); }
		if (empty($value)) { $value = $this->user->getTimezone(); }
		$selectTimezone = new HTMLSelect($inputParentElement,$inputName);
		$this->user->setTimezoneId($selectTimezone->getAttribute('id'));
		new HTMLLabel($labelParentElement,$label,$selectTimezone->getAttribute('id'));
		$timezone_identifiers = DateTimeZone::listIdentifiers();
		foreach( $timezone_identifiers as $entry ){
			if ( preg_match( '/^(America|Antartica|Arctic|Asia|Atlantic|Europe|Indian|Pacific)\//', $entry ) ){
				$ex=explode("/",$entry);
				if (!isset($continent) or $continent!=$ex[0]){
					$optgroup = new HTMLOptGroup($selectTimezone,$ex[0]);
				}
				$continent=$ex[0];
				(!empty($ex[2])) ? $city=$ex[1]." - ".$ex[2] : $city=$ex[1];
				($entry==$value) ? $selected = TRUE : $selected = FALSE;
				new HTMLOption($optgroup,$city,$entry,$selected);
			}
		}
	}
}