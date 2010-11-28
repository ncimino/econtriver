<?php
class Login {
	private $email;
	private $user;

	function __construct($parentElement,$siteInfo,$user) {
		$this->user = $user;
		$DivLogin = new HTMLDiv($parentElement,'login');
		$DivLogin->setAttribute('style','right: '.$siteInfo->getLogoWidth().';');
		if ($user->verifyUser()) {
			$this->email = new HTMLText($DivLogin,$user->getEmail());
			new HTMLText($DivLogin,' - ');
			new HTML_Anchor($DivLogin,'profile.php','Profile');
			new HTMLText($DivLogin,' - ');
			new HTML_Anchor($DivLogin,'index.php?logout=1','Logout');
		} else {
			$FormLogin = new HTMLForm($DivLogin,'index.php','login_form','login');
			$PLogin = new HTMLParagraph($FormLogin);
			new HTMLLabel($PLogin,'Email or Username:','email_input');
			$value = (isset($_POST['email'])) ? $_POST['email'] : '';
			new HTMLInputText($PLogin,'email',$value,'email_input');
			new HTMLLabel($PLogin,' Password:','password_input');
			$inputPassword = new HTMLInputPassword($PLogin,'password','password_input');
			new HTMLText($PLogin,' ');
			new HTMLInputSubmit($PLogin,'Login','Login','login_form_submit','submit_as_text');
			new HTMLText($PLogin,' - ');
			new HTML_Anchor($PLogin,'register.php','Register');
			if(empty($_POST['email'])) {
				new HTMLScript($PLogin,"document.getElementById(\"email_input\").focus();");
			} else {
				new HTMLScript($PLogin,"document.getElementById(\"password_input\").focus();");
			}
		}
	}

	public function updateEmail() {
		$this->email->replaceData($this->user->getEmail());
	}
}
?>