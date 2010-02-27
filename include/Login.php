<?php
class Login {
  function __construct($DivBanner,$user) {
    $DivLogin = new HTMLDiv($DivBanner,'login');
    if ($user->verifyUser()) {
      new HTMLText($DivLogin,$user->getEmail());
      new HTMLText($DivLogin,' - ');
      new HTMLAnchor($DivLogin,'profile.php','Manage Account');
      new HTMLText($DivLogin,' - ');
      new HTMLAnchor($DivLogin,'index.php?logout=1','Logout');
    } else {
      $FormLogin = new HTMLForm($DivLogin,'index.php','login');
      $PLogin = new HTMLParagraph($FormLogin);
      new HTMLLabel($PLogin,'Email:','email_input');      
      new HTMLInputText($PLogin,'email',$_POST['email']);
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
}
?>