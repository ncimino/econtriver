<?php
class User {
  private $user_id_int;
  private $user_pw_str;
  private $user_email_str;
  
  public function __construct ($user_email_str) {
    $this->user_email_str = $user_email_str;
  }
  
  private function determineUserId () {
    $this->user_id_int = $user_id_int;
  }
  
  protected function verifyUser ($user_pw_str) {
    
  }
  
  public function getUserId () {
    return $this->user_id_int;
  }
  
  public function getUserEmail () {
    return $this->user_email_str;
  }
}
?>