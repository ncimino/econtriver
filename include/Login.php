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
			new HTMLAnchor($DivLogin,'profile.php','Profile');
			new HTMLText($DivLogin,' - ');
			new HTMLAnchor($DivLogin,'index.php?logout=1','Logout');
		} else {
			$FormLogin = new HTMLForm($DivLogin,'index.php','login');
			$PLogin = new HTMLParagraph($FormLogin);
			new HTMLLabel($PLogin,'Email:','email_input');
			$value = (isset($_POST['email'])) ? $_POST['email'] : '';
			new HTMLInputText($PLogin,'email',$value);
			new HTMLLabel($PLogin,' Password:','password_input');
			new HTMLInputPassword($PLogin,'password');
			new HTMLText($PLogin,' ');
			new HTMLAnchor($PLogin,'javascript:submitForm(\'login_form\')','Login');
			new HTMLText($PLogin,' - ');
			new HTMLAnchor($PLogin,'register.php','Register');
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