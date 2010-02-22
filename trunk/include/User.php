<?php
class User {
  private $user_arr = array();
  private $result;
  private $db_obj;

  public function __construct ($db_obj) {
    $this->db_obj = $db_obj;
    if (isset($_COOKIE['user_id'])) {
      $this->getUserById($_COOKIE['user_id']);
    } else {
      $this->result = false;
    }
  }

  public function getUserById($user_id_int) {
    try {
      $this->result = $this->db_obj->query("SELECT * FROM user WHERE user_id=".$user_id_int.";");
      $this->user_arr = $this->db_obj->fetch();
      return $this->user_arr;
    } catch (Exception $err) {
      throw $err;
    }
  }
  
  public function getUserByHandle($handle_str) {
    try {
      $this->result = $this->db_obj->query("SELECT * FROM user WHERE handle=".strtolower($handle_str).";");
      $this->user_arr = $this->db_obj->fetch();
      return $this->user_arr;
    } catch (Exception $err) {
      throw $err;
    }
  }

  public function getUserByEmail($user_email_str) {
    try {
      $this->result = $this->db_obj->query("SELECT * FROM user WHERE email=".strtolower($user_email_str).";");
      $this->user_arr = $this->db_obj->fetch();
      return $this->user_arr;
    } catch (Exception $err) {
      throw $err;
    }
  }

  public function setPassword($password_str) { $this->user_arr['password'] = $password_str; }
  public function setEmail($email_str) { $this->user_arr['email'] = $email_str; }
  public function setTimezone($timezone_str) { $this->user_arr['timezone'] = $timezone_str; }
  public function setDateFormat($date_format_str) { $this->user_arr['date_format'] = $date_format_str; }
  public function setSubcatFirst($subcat_first_bit) { $this->user_arr['subcat_first'] = $subcat_first_bit; }
  public function setActive($active_bit) { $this->user_arr['active'] = $active_bit; }
  public function getEmail() { return $this->user_arr['email']; }
  public function getTimezone() { return $this->user_arr['timezone']; }
  public function getDateFormat() { return $this->user_arr['date_format']; }
  public function getSubcatFirst() { return $this->user_arr['subcat_first']; }
  public function getActive() { return $this->user_arr['active']; }
  
  public function commitUser() {
    try {
      $sql = "INSERT INTO user (password,email,timezone,date_format,subcat_first,active)
                        VALUES (".$this->user_arr['password'].",".
      $this->user_arr['email'].",".
      $this->user_arr['timezone'].",".
      $this->user_arr['date_format'].",".
      $this->user_arr['subcat_first'].",".
      $this->user_arr['active']."');";
      $this->result = $this->db_obj->query($sql);
      return $this->result;
    } catch (Exception $err) {
      throw $err;
    }
  }

  public function verifyUser() {
    (isset($this->user_arr['user_id'])) ? $user_id = $this->user_arr['user_id'] : $user_id = "";
    (isset($this->user_arr['password'])) ? $password = $this->user_arr['password'] : $password = "";
    (isset($this->user_arr['active'])) ? $active = $this->user_arr['active'] : $active = 0;
    (isset($_COOKIE['user_id'])) ? $cookie_user_id = $_COOKIE['user_id'] : $cookie_user_id = "";
    (isset($_COOKIE['password'])) ? $cookie_password = $_COOKIE['user_id'] : $cookie_password = "";
    if (($user_id == $cookie_user_id) and
    ($password == $cookie_password) and
    ($active)) {
      return true;
    } else {
      return false;
    }
  }
}
?>