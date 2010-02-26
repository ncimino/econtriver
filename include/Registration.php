<?php
class Registration {
  function __construct($parentElement,$user) {
    $FormReg = new HTMLForm($parentElement,'register');
    $PReg = new HTMLParagraph($FormReg);
    if ($_POST['register']=='1') {
      if ($this->addUser($PReg,$user)) {
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
    $TableReg = new Table($parentElement,7,2,'register');
    $TableReg->table->setAttribute( 'width', '50%' );
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
    (!empty($_POST['format'])) ? $format = $_POST['format'] : $format = "Y-m-d";
    new HTMLInputText($TableReg->cells[5][1],'format',$format);
    new HTMLLabel($TableReg->cells[6][0],'Timezone:','timezone_select');
    $selectTimeZone = new HTMLSelect($TableReg->cells[6][1],'timezone');
    $timezone_identifiers = DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY,'US');
    foreach( $timezone_identifiers as $value ){
      if ( preg_match( '/^(America|Antartica|Arctic|Asia|Atlantic|Europe|Indian|Pacific)\//', $value ) ){
        $ex=explode("/",$value);//obtain continent,city
        if ($continent!=$ex[0]){
          $optgroup = new HTMLOptGroup($selectTimeZone,$ex[0]);
        }
        $city=$ex[1];
        if (!empty($ex[2])) { $city.=" - ".$ex[2]; }
        $continent=$ex[0];
        new HTMLOption($optgroup,$city,$value);
      }
    }
    new HTMLBr($parentElement);
    new HTMLBr($parentElement);
    new HTMLInputSubmit($parentElement,'reg_submit','Register');
  }
  function addUser($parentElement,$user) {
    $user->setPassword($_POST['reg_pw']);
    $user->setHandle($_POST['handle']);
    $user->setEmail($_POST['reg_email']);
    $user->setTimezone($_POST['timezone']);
    $user->setDateFormat($_POST['format']);
    $user->setSubcatFirst(1);
    $user->setActive(1);
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
    } elseif (!$user->commitUser()) {
      new HTMLSpan($parentElement,'Error: ','error');
      $msg = 'This email has already been registered'; // If there were any problems commiting the user this will be executed
      new HTMLText($parentElement,$msg);
      return FALSE;
    } else {
      return TRUE;
    }
    new HTMLBr($parentElement);
  }
}