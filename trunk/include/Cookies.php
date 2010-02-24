<?php
class Cookies {
  private $userId;
  private $password;
  function __construct($salt) {
    (isset($_POST['user_id'])) ? $this->setUserId($_POST['user_id']) : $this->setUserId($_COOKIE['user_id']);
    (isset($_POST['password'])) ? $this->setPassword(crypt($_POST['password'],$salt)) : $this->setPassword($_COOKIE['password']);
    $this->setCookies();
  }
  function setCookies() {
    setcookie("user_id", $this->userId, time()+60*60*24*7);
    setcookie("password", $this->password, time()+60*60*24*7);
  }
  function setUserId($userId) { $this->userId = $userId; }
  function setPassword($password) { $this->password = $password; }
  function getUserId() { return $this->userId; }
  function getPassword() { return $this->password; }
}