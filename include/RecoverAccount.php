<?php
class RecoverAccount {
  private $focusId = 'recover_email_input';
  private $siteInfo;
  private $infoMsg;
  private $user;

  function __construct($parentElement,$infoMsg,$siteInfo,$user) {
    $this->user = $user;
    $this->infoMsg = $infoMsg;
    $this->siteInfo = $siteInfo;
    $this->user->setFromPost('recover_email','recover_email');
    $this->buildRecoveryForm($parentElement);
    if ($_POST['recover']=='1') {

      if ($this->user->getUserByEmail(false) or $this->user->setUserByHandle()) {
        $this->user->setUserByEmail();
        $this->focusId = NULL;
        if ($this->sendRecoveryEmail()) {
          $this->infoMsg->addMessage(2,'An email has been sent to the email on file.');
        } else {
          $this->infoMsg->addMessage(-1,'The user was found, but we were unable to send a recovery email.');
        }
      } else {
        $this->infoMsg->addMessage(0,'This email or user name was not found.');
      }

    }
    if (!empty($this->focusId)) {
      new HTMLScript($parentElement,"document.getElementById(\"" . $this->focusId . "\").focus();");
    }
  }

  function buildRecoveryForm($parentElement) {
    $userInputs = new UserInputs($this->user);
    $FormRecover = new HTMLForm($parentElement,'recover.php','recover_email');
    new HTMLInputHidden($FormRecover,'recover','1');
    new HTMLText($FormRecover,'If you know the email or user name that was last set then enter it here.');
    $TableRecover = new Table($FormRecover,1,2,'recover_email');
    $userInputs->inputEmail($TableRecover->cells[0][1],$TableRecover->cells[0][0]);
    new HTMLInputSubmit($FormRecover,'recover_email_submit','Recover');
  }

  function sendRecoveryEmail() {
    $email = new EmailDocument($this->siteInfo,$this->user,"Account Restoration");
    $sendEmail = new SendEmail();
    $sendEmail->addTo($this->user->getEmail());
    $sendEmail->setFrom($this->siteInfo->getFromEmail());
    $sendEmail->setSubject("eContriver - Account Recovery");
    $sendEmail->setContent($email->printPage());
    return $sendEmail->send();
  }

}