<?php
class Registration {
  function __construct() {
    $FormLogin = new HTMLForm($DivLogin,'register');
    $PReg = new HTMLParagraph($FormLogin);
    new HTMLLabel($PReg,'Email:','email_input');
    new HTMLInputText($PReg,'email');
    new HTMLLabel($PReg,' Password:','password_input');
    new HTMLInputText($PReg,'password');
    new HTMLText($PReg,' ');
    new HTMLAnchor($PReg,'javascript:submitForm(\'login_form\')','Login');
    new HTMLText($PReg,' - ');
    new HTMLAnchor($PReg,'register.php','Register');
  }
}