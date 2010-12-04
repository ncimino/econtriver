<?php
class Registration {
	private $focusId = 'reg_email_input';
	private $siteInfo;
	private $infoMsg;
	private $user;

	function __construct($parentElement,$infoMsg,$siteInfo,$user) {
		$this->user = $user;
		$this->infoMsg = $infoMsg;
		$this->siteInfo = $siteInfo;
		
		$this->user->setInputNames('reg_email','handle','reg_pw','ver_pw','timezone','format');
		$this->user->setFromPost();
		 
		$FormReg = new HTML_Form($parentElement,'register.php','register');
		$FormReg->setAttribute( 'name', 'register' );
		$PReg = new HTML_Paragraph($FormReg);
		$this->buildRegistrationForm($FormReg);

		if (isset($_POST['register']) and $_POST['register']) { // if true the user submitted a registration form
			if ($this->addUser()) {
				$this->sendRegistrationEmail();
				new HTML_Text($parentElement,'Registration successful. Please login.');
				new HTML_Script($parentElement,"document.getElementById(\"email_input\").value=\"" . $user->getHandle() . "\";");
				$this->focusId='password_input';
				$FormReg->remove();
			}
		}
		if (!empty($this->focusId)) {
			new HTML_Script($parentElement,"document.getElementById(\"" . $this->focusId . "\").focus();");
		}
	}

	function buildRegistrationForm($parentElement) {
		new HTML_InputHidden($parentElement,'register','1');
		$TableReg = new Table($parentElement,7,2,'register');
		$TableReg->table->setAttribute( 'width', '500px' );

		$userInputs = new UserInputs($this->user);
		$userInputs->inputEmail($TableReg->cells[0][1],$TableReg->cells[0][0],'Email*:');
		$userInputs->inputPassword($TableReg->cells[1][1],$TableReg->cells[1][0],'Password*:');
		$userInputs->inputVerPassword($TableReg->cells[2][1],$TableReg->cells[2][0],'Verify Password*:');
		$userInputs->inputHandle($TableReg->cells[3][1],$TableReg->cells[3][0],'User Name*:');
		new HTML_Text($TableReg->cells[4][0],'The user name is required and can be used in place of your email for login and for project sharing.');
		$TableReg->cells[4][0]->setAttribute( 'colspan', '2' );
		$userInputs->selectFormat($TableReg->cells[5][1],$TableReg->cells[5][0]);
		$userInputs->selectTimezone($TableReg->cells[6][1],$TableReg->cells[6][0]);

		new HTML_Br($parentElement);
		new HTML_Paragraph($parentElement,'We will not sell, trade, or give any of your information away.
		You will not receive newsletter emails from us. We will send you an email to recover your account information
		if you request it when you forget your password.');
		new HTML_Br($parentElement);
		new HTML_InputSubmit($parentElement,'reg_submit','Register');
	}

	function addUser() {
		if (!$this->user->commitUser()) {
			$this->focusId = $this->user->getErrorId();
			return FALSE;
		} else {
			$this->focusId = NULL;
			return TRUE;
		}
	}
	
	function sendRegistrationEmail() {
		$email = new EmailDocument($this->siteInfo,$this->user,"eContriver - Account Registration");

		$h4SubTitle = new HTML_Heading($email->content,4,"Welcome to eContriver!");
		$h4SubTitle->setAttribute('style','color:gray;');
		new HTML_Paragraph($email->content,'Thank you for registering with eContriver.');
		new HTML_Paragraph($email->content,'To access the site you can follow this link directly:');
		$mainLink = $this->siteInfo->getSiteHTTP();
		new HTML_Anchor($email->content,$mainLink,'eContriver');

		$sendEmail = new SendEmail();
		$sendEmail->addTo($this->user->getEmail());
		$sendEmail->setFrom($this->siteInfo->getFromEmail());
		$sendEmail->setSubject("eContriver - Account Registration");
		$sendEmail->setContent($email->printPage());
		return $sendEmail->send();
	}
}