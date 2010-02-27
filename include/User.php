<?php
class User {
  private $user_arr = array();
  //private $result;
  private $DB;
  private $cookies;
  private $loginFromPost = false;
  //private $userLoggedIn = true;
  private $salt;

  public function __construct ($DB,$siteInfo) {
    $this->DB = $DB;
    $this->salt = $siteInfo->getSalt();
    $this->cookies = new Cookies();
    if (isset($_POST['email'])) { // if true then user is trying to login
      if ($this->getUserByEmail($_POST['email'])) { // if true, then the emails was found
        $this->cookies->setCookies($this->user_arr['user_id'],$this->user_arr['password']); // set cookies with id and pw
        if ($this->user_arr['password']==crypt($_POST['password'],$this->salt)) { // if pw is right log them in
          $this->loginFromPost = true;
        } else {
          //echo "This password is incorrect. Forgot Password?";
          //$this->userLoggedIn = false;
        }
      } else {
        //echo "This email was not found in the database.";
        //$this->userLoggedIn = false;
      }
    } else {
      $this->getUserById($this->cookies->getUserId());
    }
  }

  public function getUserById($user_id_int) {
    try {
      //$this->result =
      $this->DB->query("SELECT * FROM user WHERE user_id='".$user_id_int."';");
      $this->user_arr = $this->DB->fetch();
      return $this->user_arr;
    } catch (Exception $err) {
      throw $err;
    }
  }

  public function getUserByHandle($handle_str) {
    try {
      //$this->result =
      $this->DB->query("SELECT * FROM user WHERE handle='".strtolower($handle_str)."';");
      $this->user_arr = $this->DB->fetch();
      return $this->user_arr;
    } catch (Exception $err) {
      throw $err;
    }
  }

  public function getUserByEmail($user_email_str) {
    try {
      //$this->result =
      $this->DB->query("SELECT * FROM user WHERE email='".strtolower($user_email_str)."';");
      $this->user_arr = $this->DB->fetch();
      return $this->user_arr;
    } catch (Exception $err) {
      throw $err;
    }
  }

  public function setPassword($password_str) { $this->user_arr['password'] = crypt($password_str,$this->salt); }
  public function setHandle($handle_str) { $this->user_arr['handle'] = $handle_str; }
  public function setEmail($email_str) { $this->user_arr['email'] = strtolower($email_str); }
  public function setTimezone($timezone_str) { $this->user_arr['timezone'] = $timezone_str; }
  public function setDateFormat($date_format_str) { $this->user_arr['date_format'] = $date_format_str; }
  public function setSubcatFirst($subcat_first_bit) { $this->user_arr['subcat_first'] = $subcat_first_bit; }
  public function setActive($active_bit) { $this->user_arr['active'] = $active_bit; }

  public function getHandle() { return $this->user_arr['handle']; }
  public function getEmail() { return $this->user_arr['email']; }
  public function getTimezone() { return $this->user_arr['timezone']; }
  public function getDateFormat() { return $this->user_arr['date_format']; }
  public function getSubcatFirst() { return $this->user_arr['subcat_first']; }
  public function getActive() { return $this->user_arr['active']; }
  public function getLoginFail() { return $this->loginFailed; }

  public function commitUser() {
    try {
      if (!($this->userExists)) {
        $sql = "INSERT INTO user (password,handle,email,timezone,date_format,subcat_first,active)
                        VALUES ('".$this->user_arr['password']."','".
        $this->user_arr['handle']."','".
        $this->user_arr['email']."','".
        $this->user_arr['timezone']."','".
        $this->user_arr['date_format']."',".
        $this->user_arr['subcat_first'].",".
        $this->user_arr['active'].");";
        //$this->result =
        return $this->DB->query($sql);
        //return $this->result;
      } else {
        return false;
      }
    } catch (Exception $err) {
      echo $err;
    }
  }

  public function verifyUser() {
    (isset($this->user_arr['user_id'])) ? $user_id = $this->user_arr['user_id'] : $user_id = "";
    (isset($this->user_arr['password'])) ? $password = $this->user_arr['password'] : $password = "";
    (isset($this->user_arr['active'])) ? $active = $this->user_arr['active'] : $active = 0;
    if ((($user_id == $this->cookies->getUserId()) and
    ($password == $this->cookies->getPassword()) and
    ($active)) or
    ($this->loginFromPost)) {
      if ($_GET['logout']==1) {
        $this->cookies->destroyPassword();
        return false;
      } else {
        return true;
      }
    } else {
      return false;
    }
  }
}
?>