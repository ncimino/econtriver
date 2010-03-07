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
    $this->setInputNames('email','email','password');
    if ($this->setFromPost()) { // if true then user is trying to login
      if ($this->verifyUser()) { // the email was found with a matching password, currentUser was updated
        $this->setCookies();
      } else { // the email was not found with a matching password
        $this->infoMsg->addMessage(0,'Login info is incorrect.','Forgot Info?','recover.php');
      }
    }
  }

  public function setCookies() {
    $this->cookies->setCookies($this->currentUser['user_id'],$this->currentUser['password']);
  }

  public function getUserById($id=NULL) {
    if(empty($id)) { $id = $this->currentUser['user_id']; }
    $this->DB->query("SELECT * FROM user WHERE user_id='".mysql_real_escape_string($id)."';");
    return $this->DB->fetch();
  }

  public function setUserById($id) {
    $this->currentUser = $this->getUserById($id);
    return $this->currentUser;
  }

  public function getUserByHandle($reportMsg=true,$handle=NULL) {
    if(empty($handle)) { $handle = $this->currentUser['handle']; }
    $this->DB->query("SELECT * FROM user WHERE handle='".mysql_real_escape_string($handle)."';");
    $fetch = $this->DB->fetch();
    if ($reportMsg and $fetch) {
      $this->infoMsg->addMessage(0,'This User Name has already been used. Please try a different one.');
    }
    return $fetch;
  }

  public function setUserByHandle($handle=NULL) {
    $this->currentUser = $this->getUserByHandle(false,$handle);
    return $this->currentUser;
  }

  public function getUserByEmail($reportMsg=true,$email=NULL) {
    if(empty($email)) { $email = $this->currentUser['email']; }
    $this->DB->query("SELECT * FROM user WHERE email='".mysql_real_escape_string(strtolower($email))."';");
    $fetch = $this->DB->fetch();
    if ($reportMsg and $fetch) {
      $this->infoMsg->addMessage(0,'This email has already been registered.','Forgot Info?','recover.php');
    }
    return $fetch;
  }

  public function setUserByEmail($email=NULL) {
    $this->currentUser = $this->getUserByEmail(false,$email);
    return $this->currentUser;
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

  public function getUserId() { return $this->currentUser['user_id']; }
  public function getPassword() { return $this->currentUser['password']; }
  public function getVerPassword() { return $this->currentUser['ver_password']; }
  public function getHandle() { return $this->currentUser['handle']; }
  public function getEmail() { return $this->currentUser['email']; }
  public function getTimezone() { return $this->currentUser['timezone']; }
  public function getDateFormat() { return $this->currentUser['date_format']; }
  public function getActive() { return $this->currentUser['active']; }
  public function getTime() { return time(); }

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

  public function clearNames() { unset($this->currentUserNames); }
  public function clearIds() { unset($this->currentUserIds); }
  public function clearUser() { unset($this->currentUser); }

  // Ids are set when UserInputs are built, this means that the inputs must be added to the HTML Document,
  // before the errorId can be used correctly.
  public function getErrorId() {
    $this->setErrorId();
    return $this->errorId;
  }

  public function commitUser() {
    try {
      if ($this->validateUserInfo()) {
        $sql = "INSERT INTO user (password,handle,email,timezone,date_format,active)
                        VALUES ('".$this->currentUser['password']."','".
        $this->currentUser['handle']."','".
        $this->currentUser['email']."','".
        $this->currentUser['timezone']."','".
        $this->currentUser['date_format']."',".
        $this->currentUser['active'].");";
        $result = $this->DB->query($sql);
        return $result;
      } else {
        return false;
      }
    } catch (Exception $err) {
      echo $err;
    }
  }

  public function updateUser($dupEmailCheck=TRUE,$dupHandleCheck=TRUE,$passwordCheck=TRUE) {
    try {
      if ($this->validateUserInfo($dupEmailCheck,$dupHandleCheck,$passwordCheck)) {
        $sql = "UPDATE user
				SET password='".$this->currentUser['password']."', 
					handle='".$this->currentUser['handle']."',
					email='".$this->currentUser['email']."',
					timezone='".$this->currentUser['timezone']."',
					date_format='".$this->currentUser['date_format']."',
					active=".$this->currentUser['active']."
				WHERE user_id=".$this->currentUser['user_id'].";";
        $result = $this->DB->query($sql);
        return $result;
      } else {
        return false;
      }
    } catch (Exception $err) {
      echo $err;
    }
  }

  public function setInputNames($emailName=NULL,$handleName=NULL,$passwordName=NULL,$verPasswordName=NULL,$timezoneName=NULL,$formatName=NULL) {
    $this->clearNames();
    if (!empty($emailName)) { $this->setEmailName($emailName); }
    if (!empty($handleName)) { $this->setHandleName($handleName); }
    if (!empty($passwordName)) { $this->setPasswordName($passwordName); }
    if (!empty($verPasswordName)) { $this->setVerPasswordName($verPasswordName); }
    if (!empty($timezoneName)) { $this->setTimezoneName($timezoneName); }
    if (!empty($formatName)) { $this->setDateFormatName($formatName); }
  }

  public function setFromPost() {
    if (!empty($this->currentUserNames['email'])) { $this->setEmail($_POST[$this->currentUserNames['email']]); }
    if (!empty($this->currentUserNames['handle'])) { $this->setHandle($_POST[$this->currentUserNames['handle']]); }
    if (!empty($this->currentUserNames['password'])) { $this->setPassword($_POST[$this->currentUserNames['password']]); }
    if (!empty($this->currentUserNames['ver_password'])) { $this->setVerPassword($_POST[$this->currentUserNames['ver_password']]); }
    if (!empty($this->currentUserNames['timezone'])) { $this->setTimezone($_POST[$this->currentUserNames['timezone']]); }
    if (!empty($this->currentUserNames['date_format'])) { $this->setDateFormat($_POST[$this->currentUserNames['date_format']]); }
    if (!empty($this->currentUserNames['ver_password'])) { $this->setActive(1); } // only set if $verPasswordName is, means user is registering
    return isset($_POST[$this->currentUserNames['email']]) or isset($_POST[$this->currentUserNames['password']]);
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

  public function validateUserInfo($dupEmailCheck=TRUE,$dupHandleCheck=TRUE,$passwordCheck=TRUE) {
    $this->dupEmailCheck = $dupEmailCheck;
    $this->dupHandleCheck = $dupHandleCheck;
    $this->passwordCheck = $passwordCheck;
    if(!$this->validateEmail() ) { return FALSE; }
    elseif ($dupEmailCheck and $this->getUserByEmail($dupEmailCheck)) { return FALSE; }
    elseif(!$this->validateHandle()) { return FALSE; }
    elseif ($dupHandleCheck and $this->getUserByHandle($dupHandleCheck)) { return FALSE; }
    elseif ($passwordCheck and !$this->validatePassword($passwordCheck)){ return FALSE; }
    elseif ($passwordCheck and !$this->validateVerPassword($passwordCheck)){ return FALSE; }
    else {return TRUE;}
  }

  public function setErrorId() {
    if(!$this->validateEmail(false)) { $this->errorId = $this->getEmailId(); }
    elseif ($this->dupEmailCheck and $this->getUserByEmail(false)) { $this->errorId = $this->getEmailId(); }
    elseif (!$this->validateHandle(false)) { $this->errorId = $this->getHandleId(); }
    elseif ($this->dupHandleCheck and $this->getUserByHandle(false)) { $this->errorId = $this->getHandleId(); }
    elseif ($this->passwordCheck and !$this->validatePassword(false)){ $this->errorId = $this->getPasswordId(); }
    elseif ($this->passwordCheck and !$this->validateVerPassword(false)){ $this->errorId = $this->getPasswordId(); }
    else { $this->errorId = NULL; }
  }
}
?>