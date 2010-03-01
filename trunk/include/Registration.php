<?php
class Registration {
  private $focusId = 'reg_email_input';
  private $user;
  
  function __construct($parentElement,$infoMsg,$user) {
    $this->user = $user;
    $this->user->setFromPost('reg_email','handle','reg_pw','ver_pw','timezone','format');
     
    $FormReg = new HTMLForm($parentElement,'register.php','register');
    $FormReg->setAttribute( 'name', 'register' );
    $PReg = new HTMLParagraph($FormReg);
    $this->buildRegistrationForm($FormReg);

    if ($_POST['register']=='1') { // if true the user submitted a registration form
      if ($this->addUser()) {
        new HTMLText($parentElement,'Registration successful.');
        new HTMLScript($parentElement,"document.getElementById(\"email_input\").value=\"" . $user->getEmail() . "\";");
        $this->focusId='password_input';
        $FormReg->remove();
      }
    }
    if (!empty($this->focusId)) {
      new HTMLScript($parentElement,"document.getElementById(\"" . $this->focusId . "\").focus();");
    }
  }
  
  function buildRegistrationForm($parentElement) {
    new HTMLInputHidden($parentElement,'register','1');
    $TableReg = new Table($parentElement,7,2,'register');
    $TableReg->table->setAttribute( 'width', '500px' );

    $userInputs = new UserInputs($this->user);
    $userInputs->inputEmail($TableReg->cells[0][1],$TableReg->cells[0][0],'Email*:');
    $userInputs->inputPassword($TableReg->cells[1][1],$TableReg->cells[1][0],'Password*:');
    $userInputs->inputVerPassword($TableReg->cells[2][1],$TableReg->cells[2][0],'Verify Password*:');
    $userInputs->inputHandle($TableReg->cells[3][1],$TableReg->cells[3][0],'User Name*:');
    new HTMLText($TableReg->cells[4][0],'The user name is required and can be used in place of your email for login and for project sharing.');
    $TableReg->cells[4][0]->setAttribute( 'colspan', '2' );
    $userInputs->selectFormat($TableReg->cells[5][1],$TableReg->cells[5][0]);
    $userInputs->selectTimezone($TableReg->cells[6][1],$TableReg->cells[6][0]);
    
    new HTMLBr($parentElement);
    new HTMLInputSubmit($parentElement,'reg_submit','Register');
  }
  
  function addUser() {
    if (!$this->user->commitUser()) {
      $this->focusId = $this->user->getErrorId();
      return FALSE;
    } else {
      $this->focusId = NULL;
      return TRUE;
    }
  }
}