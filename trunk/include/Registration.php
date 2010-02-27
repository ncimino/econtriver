<?php
class Registration {
  private $focusId = 'reg_email_input';
  function __construct($parentElement,$infoMsg,$user) {
    $FormReg = new HTMLForm($parentElement,'register.php','register');
    $FormReg->setAttribute( 'name', 'register' );
    $PReg = new HTMLParagraph($FormReg);
    if ($_POST['register']=='1') { // if true the user submitted a registration form
      
      if ($this->addUser($PReg,$infoMsg,$user)) {
        new HTMLText($PReg,'Registration successful.');
        new HTMLScript($FormReg,"document.getElementById(\"email_input\").value=\"" . $_POST['reg_email'] . "\";");
        $this->focusId='password_input';
        //new HTMLScript($FormReg,"document.getElementById(\"password_input\").focus();");
      } else {
        $this->buildRegistrationForm($FormReg);
      }
      
    } else {
      $this->buildRegistrationForm($FormReg);
    }
    if (!empty($this->focusId)) {
      new HTMLScript($FormReg,"document.getElementById(\"" . $this->focusId . "\").focus();");
    }
  }
  
  function buildRegistrationForm($parentElement) {
    new HTMLInputHidden($parentElement,'register','1');
    $TableReg = new Table($parentElement,7,2,'register');
    $TableReg->table->setAttribute( 'width', '500px' );

    new HTMLLabel($TableReg->cells[0][0],'Email*:','reg_email_input');
    new HTMLInputText($TableReg->cells[0][1],'reg_email',$_POST['reg_email']);

    new HTMLLabel($TableReg->cells[1][0],'Password*:','reg_pw_input');
    new HTMLInputPassword($TableReg->cells[1][1],'reg_pw');

    new HTMLLabel($TableReg->cells[2][0],'Verify Password*:','ver_pw_input');
    new HTMLInputPassword($TableReg->cells[2][1],'ver_pw');

    new HTMLLabel($TableReg->cells[3][0],'User Name*:','handle_input');
    new HTMLInputText($TableReg->cells[3][1],'handle',$_POST['handle']);
    new HTMLText($TableReg->cells[4][0],'The user name is required and can be used in place of your email for login and for project sharing.');
    $TableReg->cells[4][0]->setAttribute( 'colspan', '2' );

    new HTMLLabel($TableReg->cells[5][0],'Date Format*:','format_input');
    $selectFormat = new HTMLSelect($TableReg->cells[5][1],'format');
    $format[0]['php'] = "Y-m-d";
    $format[0]['display'] = "YYYY-MM-DD";
    $format[1]['php'] = "m/d/Y";
    $format[1]['display'] = "MM/DD/YYYY";
    foreach ($format as $value) {
      ($value['php']==$_POST['format']) ? $selected = TRUE : $selected = FALSE;
      new HTMLOption($selectFormat,$value['display'],$value['php'],$selected);
    }

    new HTMLLabel($TableReg->cells[6][0],'Timezone:','timezone_select');
    $selectTimeZone = new HTMLSelect($TableReg->cells[6][1],'timezone');
    $timezone_identifiers = DateTimeZone::listIdentifiers();
    foreach( $timezone_identifiers as $value ){
      if ( preg_match( '/^(America|Antartica|Arctic|Asia|Atlantic|Europe|Indian|Pacific)\//', $value ) ){
        $ex=explode("/",$value);
        if ($continent!=$ex[0]){
          $optgroup = new HTMLOptGroup($selectTimeZone,$ex[0]);
        }
        $continent=$ex[0];
        (!empty($ex[2])) ? $city=$ex[1]." - ".$ex[2] : $city=$ex[1];
        ($value==$_POST['timezone']) ? $selected = TRUE : $selected = FALSE;
        new HTMLOption($optgroup,$city,$value,$selected);
      }
    }
    
    new HTMLBr($parentElement);
    new HTMLInputSubmit($parentElement,'reg_submit','Register');
  }
  
  function addUser($parentElement,$infoMsg,$user) {
    ($user->getUserByEmail($_POST['reg_email'])) ? $emailExists = TRUE : $emailExists = FALSE;
    ($user->getUserByHandle($_POST['handle'])) ? $handleExists = TRUE : $handleExists = FALSE;
    $user->setPassword($_POST['reg_pw']);
    $user->setHandle($_POST['handle']);
    $user->setEmail($_POST['reg_email']);
    $user->setTimezone($_POST['timezone']);
    $user->setDateFormat($_POST['format']);
    $user->setSubcatFirst(1);
    $user->setActive(1);
    if( !preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $user->getEmail())) {
      $infoMsg->addMessage(2,'The user email address in invalid.');
      return FALSE;
    } elseif( !preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*$/", $user->getHandle())) {
      $infoMsg->addMessage(1,'This User Name in invalid. You must use only letters, numbers, periods, underscores, and hyphens.');
      $this->focusId = "handle_input";
      return FALSE;
    } elseif ($_POST['reg_pw'] != $_POST['ver_pw']){
      $infoMsg->addMessage(0,'The passwords to not match.');
      $this->focusId = "reg_pw_input";
      return FALSE;
    } elseif ($_POST['reg_pw'] == ''){
      $infoMsg->addMessage(0,'The passwords cannot be blank.');
      $this->focusId = "reg_pw_input";
      return FALSE;
    } elseif ($emailExists) {
      $infoMsg->addMessage(0,'This email has already been registered.');
      return FALSE;
    } elseif ($handleExists) {
      $infoMsg->addMessage(0,'This User Name has already been used.');
      $this->focusId = "handle_input";
      return FALSE;
    } elseif (!$user->commitUser()) {
      $infoMsg->addMessage(0,'An unexpected error occurred while adding the user.');
      return FALSE;
    } else {
      $this->focusId = NULL;
      return TRUE;
    }
    new HTMLBr($parentElement);
  }
}