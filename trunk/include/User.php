<?php
class User {
  private $user_arr = array();
  private $result;
  public $db_obj;

  public function __construct ($db_obj) {
    $this->db_obj = $db_obj;
    if (isset($_COOKIE['user_id'])) {
      return $this->getUserById($_COOKIE['user_id']);
    } else {
      $this->result = false;
      return $this->result;
    }
  }

  public function getUserById($user_id_int) {
    if(! empty($user_id_int)) { $this->user_id_int = $user_id_int; }
    try {
      $db_obj = $this->db_obj;
      $this->result = $db_obj->query("SELECT * FROM user WHERE user_id=".$this->user_id_int.";");
      $this->user_arr = $db_obj->fetch();
      return $this->user_arr;
    } catch (Exception $err) {
      throw $err;
    }
  }

  public function getUserByEmail($user_email_str) {
    if(! empty($user_email_str)) { $this->user_email_str = $user_email_str; }
    try {
      $db_obj = $this->db_obj;
      $this->result = $db_obj->query("SELECT * FROM user WHERE email=".$this->user_id_int.";");
      $this->user_arr = $db_obj->fetch();
      return $this->user_arr;
    } catch (Exception $err) {
      throw $err;
    }
  }

  public function setPassword($password_str) {
    $this->user_arr['password'] = $password_str;
  }

  public function setEmail($email_str) {
    $this->user_arr['email'] = $email_str;
  }

  public function setTimezone($timezone_str) {
    $this->user_arr['timezone'] = $timezone_str;
  }

  public function setDateFormat($date_format_str) {
    $this->user_arr['date_format'] = $date_format_str;
  }

  public function setSubcatFirst($subcat_first_bit) {
    $this->user_arr['subcat_first'] = $subcat_first_bit;
  }

  public function setActive($active_bit) {
    $this->user_arr['active'] = $active_bit;
  }

  public function getEmail() {
    return $this->user_arr['email'];
  }

  public function getTimezone() {
    return $this->user_arr['timezone'];
  }

  public function getDateFormat() {
    return $this->user_arr['date_format'];
  }

  public function getSubcatFirst() {
    return $this->user_arr['subcat_first'];
  }

  public function getActive() {
    return $this->user_arr['active'];
  }

  public function commitUser() {
    try {
      $db_obj = $this->db_obj;
      $sql = "INSERT INTO user (password,email,timezone,date_format,subcat_first,active)
                        VALUES (".$this->user_arr['password'].",".
      $this->user_arr['email'].",".
      $this->user_arr['timezone'].",".
      $this->user_arr['date_format'].",".
      $this->user_arr['subcat_first'].",".
      $this->user_arr['active']."');";
      $this->result = $db_obj->query($sql);
      return $this->result;
    } catch (Exception $err) {
      throw $err;
    }
  }

  public function verifyUser() {
    (isset($_COOKIE['user_id'])) ? $cookie_user_id = $_COOKIE['user_id'] : $cookie_user_id = "";
    (isset($this->user_arr['user_id'])) ? $user_id = $this->user_arr['user_id'] : $user_id = "";
    (isset($_COOKIE['password'])) ? $cookie_password = $_COOKIE['user_id'] : $cookie_password = "";
    (isset($this->user_arr['password'])) ? $password = $this->user_arr['password'] : $password = "";
    (isset($this->user_arr['active'])) ? $user_is_active = $$this->user_arr['active'] : $user_is_active = 0;
    if (($user_id == $cookie_user_id) and
    ($password == $cookie_password) and
    ($user_is_active)) {
      return true;
    } else {
      return false;
    }
  }
}
?>