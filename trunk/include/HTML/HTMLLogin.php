<?php
class HTMLLogin {
  public $user_obj;
  private $html_login_arr;
  private $html_form_arr;

  public function __construct($user_obj) {
    $this->user_obj = $user_obj;
    $this->setHTMLLogin();
  }

  public function setHTMLLogin() {
    $user_obj = $this->user_obj;
    
    if ($user_obj->verifyUser()) {
      $this->html_form_arr[] = $user_obj->getEmail();
      $this->html_form_arr[] = " - ";
      $this->html_form_arr[] = new HTMLAnchor('Logout','?logout=1','logout','');
    } else {
      $html_form_obj = new HTMLForm('login','');
      $form_elements[] = new HTMLInput('email','Email:',$user_obj->getEmail(),'text');
      $form_elements[] = new HTMLInput('password','Password:','','password');
      $form_elements[] = new HTMLAnchor('Login','javascript:submitForm(\''.$html_form_obj->getFormId().'\')','login','');
      $form_elements[] = " - ";
      $form_elements[] = new HTMLAnchor('Register','register.php','register','');
      $html_form_obj->setElement($form_elements);
      $this->html_form_arr[] = $html_form_obj;
    }
    
    $this->html_login_arr[] =  "<div class=\"login\">\n";
    $this->html_login_arr[] .= $html_form_obj;
    $this->html_login_arr[] .= "</div>\n";
  }

  public function getHTMLLogin() {
    return $this->html_login_arr;
  }

  public function __toString() {
    foreach($this->getHTMLLogin() as $html_login_element)
    $html_login_str .= $html_login_element;
    return $html_login_str;
  }
}
?>