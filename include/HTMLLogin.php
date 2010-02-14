<?php
class HTMLLogin {
  private $html_login_str;
  private $html_userid_int;
  private $html_pw_str;
  
  public function __construct ($user_obj) {
    //$this->html_userid_int = $user_obj;
    //$this->html_pw_str = $html_pw_str;
    $this->setHTMLLogin($user_obj);
  }
  
  public function setHTMLLogin ($user_obj) {
    $this->html_login_str =  "<div class=\"login\">\n";
    if ($user_obj->verifyUser()) {
      $this->html_login_str .= $this->user_email_str." -\n";
      $this->html_login_str .= "<a href=\"?logout=1\">Logout</a>\n";
    }
    else {
      $this->html_login_str .= "<form method=\"post\">\n";
      $this->html_login_str .= "<input type=\"text\" name=\"email\" />\n";
      $this->html_login_str .= "<input type=\"password\" name=\"password\" />\n";
      $this->html_login_str .= "<a href=\"javascript:submitForm(this)\">Login</a> -\n";
      $this->html_login_str .= "</form>\n"; 
      $this->html_login_str .= "<a href=\"registration.php\">Register</a>\n";
    }
    $this->html_login_str .= "</div>\n";
  }
  
  public function getHTMLHeader () {
    return $this->html_login_str;
  }
}