<?php
class Cookies {
  private $userId;
  private $password;
  function __construct() {
    $this->setUserId($_COOKIE['user_id']);
    $this->setPassword($_COOKIE['password']);
  }
  function setCookies($userId=NULL,$password=NULL) {
    ($userId===NULL) ? setcookie("user_id", $this->userId, time()+60*60*24*7) : setcookie("user_id", $userId, time()+60*60*24*7) ;
    ($password===NULL) ? setcookie("password", $this->password, time()+60*60*24*7) : setcookie("password", $password, time()+60*60*24*7);
  }
  function setUserId($userId) { $this->userId = $userId; }
  function setPassword($password) { $this->password = $password; }
  function getUserId() { return $this->userId; }
  function getPassword() { return $this->password; }
}