<?php
class User {
  private $DB;
  private $siteInfo;
  private $infoMsg;
  private $cookies;
  private $currentUser = array();
  private $currentUserNames = array();
  private $currentUserIds = array();
  private $errorId = NULL;
  private $rawPassword;

  public function __construct ($DB,$siteInfo,$infoMsg) {
    $this->DB = $DB;
    $this->siteInfo = $siteInfo;
    $this->infoMsg = $infoMsg;
    $this->cookies = new Cookies();
    if ($this->setFromPost('email','email','password')) { // if true then user is trying to login
      if ($this->verifyUser()) { // the email was found with a matching password, and currentUser was updated
        $this->cookies->setCookies($this->currentUser['user_id'],$this->currentUser['password']);
      } else { // the email was not found with a matching password
        $this->infoMsg->addMessage(0,'Login info is incorrect.','Forgot Info?','recover.php');
      }
    }
  }

  public function getUserById($id=NULL) {
    if(empty($id)) { $id = $this->currentUser['user_id']; }
    try {
      $this->DB->query("SELECT * FROM user WHERE user_id='".mysql_real_escape_string($id)."';");
      return $this->DB->fetch();
    } catch (Exception $err) {
      throw $err;
    }
  }

  public function setUserById($id) {
    try {
      $this->currentUser = $this->getUserById($id);
      return $this->currentUser;
    } catch (Exception $err) {
      throw $err;
    }
  }

  public function getUserByHandle($reportMsg=true,$handle=NULL) {
    if(empty($handle)) { $handle = $this->currentUser['handle']; }
    try {
      $this->DB->query("SELECT * FROM user WHERE handle='".mysql_real_escape_string($handle)."';");
      $fetch = $this->DB->fetch();
      if ($reportMsg and $fetch) {
        $this->infoMsg->addMessage(0,'This User Name has already been used.');
      }
      return $fetch;
    } catch (Exception $err) {
      throw $err;
    }
  }

  public function setUserByHandle($handle=NULL) {
    try {
      $this->currentUser = $this->getUserByHandle(false,$handle);
      return $this->currentUser;
    } catch (Exception $err) {
      throw $err;
    }
  }

  public function getUserByEmail($reportMsg=true,$email=NULL) {
    if(empty($email)) { $email = $this->currentUser['email']; }
    try {
      $this->DB->query("SELECT * FROM user WHERE email='".mysql_real_escape_string(strtolower($email))."';");
      $fetch = $this->DB->fetch();
      if ($reportMsg and $fetch) {
        $this->infoMsg->addMessage(0,'This email has already been registered.','Forgot Info?','recover.php');
      }
      return $fetch;
    } catch (Exception $err) {
      throw $err;
    }
  }

  public function setUserByEmail($email=NULL) {
    try {
      $this->currentUser = $this->getUserByEmail(false,$email);
      return $this->currentUser;
    } catch (Exception $err) {
      throw $err;
    }
  }

  public function setPassword($value) {
    $this->currentUser['password'] = crypt($value,$this->siteInfo->getSalt());
    $this->rawPassword = $value;
  }
  public function setVerPassword($value) { $this->currentUser['ver_password'] = crypt($value,$this->siteInfo->getSalt()); }
  public function setHandle($value) { $this->currentUser['handle'] = mysql_real_escape_string($value); }
  public function setEmail($value) { $this->currentUser['email'] = mysql_real_escape_string(strtolower($value)); }
  public function setTimezone($value) { $this->currentUser['timezone'] = $value; }
  public function setDateFormat($value) { $this->currentUser['date_format'] = $value; }
  public function setSubcatFirst($value) { $this->currentUser['subcat_first'] = $value; }
  public function setActive($value) { $this->currentUser['active'] = $value; }

  public function setPasswordName($value) { $this->currentUserNames['password'] = $value; }
  public function setVerPasswordName($value) { $this->currentUserNames['ver_password'] = $value; }
  public function setHandleName($value) { $this->currentUserNames['handle'] = $value; }
  public function setEmailName($value) { $this->currentUserNames['email'] = $value; }
  public function setTimezoneName($value) { $this->currentUserNames['timezone'] = $value; }
  public function setDateFormatName($value) { $this->currentUserNames['date_format'] = $value; }

  public function setPasswordId($value) { $this->currentUserIds['password'] = $value; }
  public function setVerPasswordId($value) { $this->currentUserIds['ver_password'] = $value; }
  public function setHandleId($value) { $this->currentUserIds['handle'] = $value; }
  public function setEmailId($value) { $this->currentUserIds['email'] = $value; }
  public function setTimezoneId($value) { $this->currentUserIds['timezone'] = $value; }
  public function setDateFormatId($value) { $this->currentUserIds['date_format'] = $value; }

  public function getPassword() { return $this->currentUser['password']; }
  public function getVerPassword() { return $this->currentUser['ver_password']; }
  public function getHandle() { return $this->currentUser['handle']; }
  public function getEmail() { return $this->currentUser['email']; }
  public function getTimezone() { return $this->currentUser['timezone']; }
  public function getDateFormat() { return $this->currentUser['date_format']; }
  public function getSubcatFirst() { return $this->currentUser['subcat_first']; }
  public function getActive() { return $this->currentUser['active']; }

  public function getPasswordName() { return $this->currentUserNames['password']; }
  public function getVerPasswordName() { return $this->currentUserNames['ver_password']; }
  public function getHandleName() { return $this->currentUserNames['handle']; }
  public function getEmailName() { return $this->currentUserNames['email']; }
  public function getTimezoneName() { return $this->currentUserNames['timezone']; }
  public function getDateFormatName() { return $this->currentUserNames['date_format']; }

  public function getPasswordId() { return $this->currentUserIds['password']; }
  public function getVerPasswordId() { return $this->currentUserIds['ver_password']; }
  public function getHandleId() { return $this->currentUserIds['handle']; }
  public function getEmailId() { return $this->currentUserIds['email']; }
  public function getTimezoneId() { return $this->currentUserIds['timezone']; }
  public function getDateFormatId() { return $this->currentUserIds['date_format']; }

  public function getLoginFail() { return $this->loginFailed; }
  public function getErrorId() { return $this->errorId; }

  public function commitUser() {
    try {
      if ($this->validateUserInfo()) {
        $sql = "INSERT INTO user (password,handle,email,timezone,date_format,subcat_first,active)
                        VALUES ('".$this->currentUser['password']."','".
        $this->currentUser['handle']."','".
        $this->currentUser['email']."','".
        $this->currentUser['timezone']."','".
        $this->currentUser['date_format']."',".
        $this->currentUser['subcat_first'].",".
        $this->currentUser['active'].");";
        $result = $this->DB->query($sql);
        if (!$result) { $this->infoMsg->addMessage(-1,'An unexpected error occurred while adding the user.'); }
        return $result;
      } else {
        return false;
      }
    } catch (Exception $err) {
      echo $err;
    }
  }

  public function setFromPost($emailName=NULL,$handleName=NULL,$passwordName=NULL,$verPasswordName=NULL,$timezoneName=NULL,$formatName=NULL) {
    if (!empty($emailName)) {
      $this->setEmailName($emailName);
      $this->setEmail($_POST[$emailName]);
    }
    if (!empty($handleName)) {
      $this->setHandleName($handleName);
      $this->setHandle($_POST[$handleName]);
    }
    if (!empty($passwordName)) {
      $this->setPassword($_POST[$passwordName]);
      $this->setPasswordName($passwordName);
    }
    if (!empty($verPasswordName)) {
      $this->setVerPasswordName($verPasswordName);
      $this->setVerPassword($_POST[$verPasswordName]);
    }
    if (!empty($timezoneName)) {
      $this->setTimezoneName($timezoneName);
      $this->setTimezone($_POST[$timezoneName]);
    }
    if (!empty($formatName)) {
      $this->setDateFormatName($formatName);
      $this->setDateFormat($_POST[$formatName]);
    }
    if (!empty($verPasswordName)) { $this->setSubcatFirst(1); } // only set if $verPasswordName is, means user is registering
    if (!empty($verPasswordName)) { $this->setActive(1); } // only set if $verPasswordName is, means user is registering

    return isset($_POST[$emailName]);
  }

  public function verifyUser() {
    $userByEmail = $this->getUserByEmail(false);
    $userByHandle = $this->getUserByHandle(false);
    $userById = $this->getUserById($this->cookies->getUserId());

    if ($_GET['logout']==1) {
      $this->cookies->destroyPassword();
      return false;
    } elseif ($userByEmail and $userByEmail['password']==$this->getPassword()) {
      $this->setUserByEmail();
      return true;
    } elseif ($userByHandle and $userByHandle['password']==$this->getPassword()) {
      $this->setUserByHandle();
      return true;
    } elseif ($userById and $userById['password']==$this->cookies->getPassword()) {
      $this->setUserById($this->cookies->getUserId());
      return true;
    } else {
      return false;
    }
  }

  public function validatePassword($reportMsg=TRUE) {
    $check = strlen($this->rawPassword)>=3;
    if($reportMsg and !$check) { $this->infoMsg->addMessage(0,'The password must be at least 3 characters.'); }
    return $check;
  }

  public function validateVerPassword($reportMsg=TRUE) {
    $check = $this->getPassword() == $this->getVerPassword();
    if($reportMsg and !$check) { $this->infoMsg->addMessage(0,'The passwords do not match.'); }
    return $check;
  }

  public function validateEmail($reportMsg=TRUE,$value=NULL) {
    if(empty($value)) { $value = $this->currentUser['email']; }
    //$check = preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $value);
    $check = preg_match( "/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/", $value);
    if($reportMsg and !$check) { $this->infoMsg->addMessage(0,'The user email address in invalid.'); }
    return $check;
  }

  public function validateHandle($value = NULL) {
    if(empty($value)) { $value = $this->currentUser['handle']; }
    $check = preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*$/", $value);
    if(!$check) { $this->infoMsg->addMessage(0,'This User Name in invalid. You must use only letters, numbers, periods, underscores, and hyphens.'); }
    return $check;
  }

  public function validateUserInfo() {
    if(!$this->validateEmail() ) {
      $this->errorId = $this->getEmailId();
      return FALSE;
    } elseif ($this->getUserByEmail()) {
      $this->errorId = $this->getEmailId();
      return FALSE;
    } elseif(!$this->validateHandle()) {
      $this->errorId = $this->getHandleId();
      return FALSE;
    } elseif ($this->getUserByHandle()) {
      $this->errorId = $this->getHandleId();
      return FALSE;
    } elseif (!$this->validateVerPassword()){
      $this->errorId = $this->getPasswordId();
      return FALSE;
    } elseif (!$this->validatePassword()){
      $this->errorId = $this->getPasswordId();
      return FALSE;
    } else {
      $this->errorId = NULL;
      return TRUE;
    }
  }
}
?>