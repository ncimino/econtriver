<?php
class Registration {
  function __construct($parentElement,$User) {
    $FormReg = new HTMLForm($parentElement,'register');
    $PReg = new HTMLParagraph($FormReg);
    if ($_POST['register']=='1') {
      if ($this->addUser($PReg,$User)) {
        new HTMLText($PReg,'Registration successful.');
      } else {
        $this->buildRegistrationTable($PReg);
      }
    } else {
      $this->buildRegistrationTable($PReg);
    }
  }
  function buildRegistrationTable($parentElement) {
    new HTMLInputHidden($parentElement,'register','1');
    $TableReg = new Table($parentElement,4,2,'register');
    new HTMLLabel($TableReg->cells[0][0],'Email*:','reg_email_input');
    new HTMLInputText($TableReg->cells[0][1],'reg_email',$_POST['reg_email']);
    new HTMLLabel($TableReg->cells[1][0],'Password*:','reg_pw_input');
    new HTMLInputPassword($TableReg->cells[1][1],'reg_pw');
    new HTMLLabel($TableReg->cells[2][0],'Verify Password*:','ver_pw_input');
    new HTMLInputPassword($TableReg->cells[2][1],'ver_pw');
    new HTMLLabel($TableReg->cells[3][0],'User Name:','username_input');
    new HTMLInputText($TableReg->cells[3][1],'handle',$_POST['handle']);
    new HTMLText($parentElement,'The user name is not required, but it can be used in place of your email for login and for account sharing.');
    new HTMLBr($parentElement);
    new HTMLBr($parentElement);
    new HTMLInputSubmit($parentElement,'reg_submit','Register');
  }
  function addUser($parentElement,$User) {
    $User->setPassword($_POST['reg_pw']);
    $User->setHandle($_POST['handle']);
    $User->setEmail($_POST['reg_email']);
    $User->setTimezone('test');
    $User->setDateFormat('test');
    $User->setSubcatFirst(1);
    $User->setActive(1);
    if( !preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $_POST['reg_email'])) {
      new HTMLSpan($parentElement,'Error: ','error');
      new HTMLText($parentElement,'The user email address in invalid.');
      return FALSE;
    } elseif ($_POST['reg_pw'] != $_POST['ver_pw']){
      new HTMLSpan($parentElement,'Error: ','error');
      new HTMLText($parentElement,'The passwords to not match.');
      return FALSE;
    } elseif ($_POST['reg_pw'] == ''){
      new HTMLSpan($parentElement,'Error: ','error');
      new HTMLText($parentElement,'The passwords cannot be blank.');
      return FALSE;
    } elseif (!$User->commitUser()) {
      new HTMLSpan($parentElement,'Error: ','error');
      $msg = 'There was a problem creating a new user. This generally occurs if this email has already been registered';
      new HTMLText($parentElement,$msg);
      return FALSE;
    } else {
      return TRUE;
    }
      new HTMLBr($parentElement);
  }
}