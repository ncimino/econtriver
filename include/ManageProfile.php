<?php
class ManageProfile {
	private $focusId = 'profile_email_input';
	private $siteInfo;
	private $infoMsg;
	private $user;

	function __construct($parentElement,$infoMsg,$siteInfo,$user,$body) {
		$this->user = $user;
		$this->infoMsg = $infoMsg;
		$this->siteInfo = $siteInfo;

		$this->user->setInputNames('profile_email','profile_handle','','','profile_timezone','profile_format');
		if (isset($_POST['profile']) and $this->siteInfo->verifyReferer()) { // User has submitted management form
			$currentEmail = $this->user->getEmail();
			$currentHandle = $this->user->getHandle();
			$this->user->setFromPost();
			$dupEmailCheck = ($currentEmail == $this->user->getEmail()) ? FALSE : TRUE; // Only check for duplicate email if it changed
			$dupHandleCheck = ($currentHandle == $this->user->getHandle()) ? FALSE : TRUE; // Only check for duplicate handle if it changed
			if ($this->user->updateUser($dupEmailCheck,$dupHandleCheck,false)) {
				$this->infoMsg->addMessage(2,'User info was updated.');
				$body->login->updateEmail(); // Update the email displayed in the login section
				$this->buildManagementForm($parentElement);
				$this->focusId = NULL;
			} else {
				$this->buildManagementForm($parentElement);
				$this->focusId = $this->user->getErrorId();
			}
		} else { // User just landed on the Manage page, no info to check yet
			$this->buildManagementForm($parentElement);
		}

		$this->user->setInputNames('','','new_password','new_ver_password');
		if (isset($_POST['passwords']) and $this->siteInfo->verifyReferer()) { // User has submitted password form
			$this->user->setFromPost();
			if ($this->user->updateUser(false,false)) {
				$this->infoMsg->addMessage(2,'Password was updated.');
				$this->user->setCookies(); // Keep the user logged in after the password change
				$this->buildPasswordForm($parentElement);
				$this->focusId = NULL;
			} else {
				$this->buildPasswordForm($parentElement);
				$this->focusId = $this->user->getErrorId();
			}
		} else { // User just landed on the Manage page, no info to check yet
			$this->buildPasswordForm($parentElement);
		}

		if (!empty($this->focusId)) {
			new HTML_Script($parentElement,"document.getElementById(\"" . $this->focusId . "\").focus();");
		}
	}

	function buildManagementForm($parentElement) {
		$formManage = new HTML_Form($parentElement,'profile.php','profile');
		new HTML_InputHidden($formManage,'profile','1');
		$TableReg = new Table($formManage,4,2,'profile');
		$TableReg->table->setAttribute( 'width', '500px' );

		$userInputs = new UserInputs($this->user);
		$userInputs->inputEmail($TableReg->cells[0][1],$TableReg->cells[0][0]);
		$userInputs->inputHandle($TableReg->cells[1][1],$TableReg->cells[1][0]);
		$userInputs->selectFormat($TableReg->cells[2][1],$TableReg->cells[2][0]);
		$userInputs->selectTimezone($TableReg->cells[3][1],$TableReg->cells[3][0]);

		new HTML_InputSubmit($formManage,'profile_submit','Update');
		new HTML_Br($formManage);
		new HTML_Br($formManage);
	}

	function buildPasswordForm($parentElement) {
		$formPassword = new HTML_Form($parentElement,'profile.php','profile');
		new HTML_InputHidden($formPassword,'passwords','1');
		$TablePas = new Table($formPassword,2,2,'passwords');
		$TablePas->table->setAttribute( 'width', '500px' );

		$userInputs = new UserInputs($this->user);
		$userInputs->inputPassword($TablePas->cells[0][1],$TablePas->cells[0][0]);
		$userInputs->inputVerPassword($TablePas->cells[1][1],$TablePas->cells[1][0]);

		new HTML_InputSubmit($formPassword,'passwords_submit','Change Password');
		new HTML_Br($formPassword);
		new HTML_Br($formPassword);
	}
}