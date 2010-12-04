<?php
class Login {
	private $email;
	private $user;

	function __construct($parentElement,$siteInfo,$user) {
		$this->user = $user;
		$DivLogin = new HTML_Div($parentElement,'login');
		$DivLogin->setAttribute('style','right: '.$siteInfo->getLogoWidth().';');
		if ($user->verifyUser()) {
			$this->email = new HTML_Text($DivLogin,$user->getEmail());
			new HTML_Text($DivLogin,' - ');
			new HTML_Anchor($DivLogin,'profile.php','Profile');
			new HTML_Text($DivLogin,' - ');
			new HTML_Anchor($DivLogin,'index.php?logout=1','Logout');
		} else {
			$FormLogin = new HTML_Form($DivLogin,'index.php','login_form','login');
			$PLogin = new HTML_Paragraph($FormLogin);
			new HTML_Label($PLogin,'Email or Username:','email_input');
			$value = (isset($_POST['email'])) ? $_POST['email'] : '';
			new HTML_InputText($PLogin,'email',$value,'email_input');
			new HTML_Label($PLogin,' Password:','password_input');
			$inputPassword = new HTML_InputPassword($PLogin,'password','password_input');
			new HTML_Text($PLogin,' ');
			new HTML_InputSubmit($PLogin,'Login','Login','login_form_submit','submit_as_text');
			new HTML_Text($PLogin,' - ');
			new HTML_Anchor($PLogin,'register.php','Register');
			if(empty($_POST['email'])) {
				new HTML_Script($PLogin,"document.getElementById(\"email_input\").focus();");
			} else {
				new HTML_Script($PLogin,"document.getElementById(\"password_input\").focus();");
			}
		}
	}

	public function updateEmail() {
		$this->email->replaceData($this->user->getEmail());
	}
}
?>