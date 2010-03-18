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

    if (isset($_GET['recover_id'])) { // User has used a link to reset password

      if ($this->verifyRecoveryLink($parentElement,$_GET['recover_id'],$_GET['verify1'],$_GET['verify2'])) {
        $this->buildResetForm($parentElement);
        $this->focusId = $this->user->getPasswordId();
      } else {
        $this->buildRecoveryForm($parentElement);
      }

    } elseif (isset($_POST['reset']) and $this->siteInfo->verifyReferer()) { // User has used entered new passwords

      if (!$this->user->getUserById($_POST['reset'])) { // Check if User ID exists
        $this->infoMsg->addMessage(0,'This link is not valid. Resubmit the information.');
        $recoveryForm = $this->buildRecoveryForm($parentElement);
      } else {
        $this->user->setUserById($_POST['reset']);
        $this->user->setPassword($_POST['reset_password']);
        $this->user->setVerPassword($_POST['reset_ver_password']);
        $resetForm = $this->buildResetForm($parentElement); // Must be set before user methods for errorIds to work
        if ($this->user->updateUser(false,false)) { // Check if password was valid and updates the user if it was
          $resetForm->remove();
          new HTMLText($parentElement,'Password reset successful.');
          $this->focusId = NULL;
        } else {
          $this->focusId = $this->user->getErrorId();
        }
      }

    } elseif (isset($_POST['recover']) and ($_POST['recover']=='1') and $this->siteInfo->verifyReferer()) { // User has submitted a user name or email for recovery

      $recoveryForm = $this->buildRecoveryForm($parentElement);
      if ($this->user->getUserByEmail(false) or $this->user->setUserByHandle()) {
        $this->user->setUserByEmail(); // two sets in the top would overwrite, once we know we have found the user we can set the other
        $this->focusId = NULL;
        if ($this->sendRecoveryEmail()) {
          $this->infoMsg->addMessage(2,'An email has been sent to the email on file.');
        } else {
          $this->infoMsg->addMessage(-1,'The user was found, but we were unable to send a recovery email.');
        }
      } else {
        $this->infoMsg->addMessage(0,'This email or user name was not found.');
      }

    } else { // No information has been entered yet
      $recoveryForm = $this->buildRecoveryForm($parentElement);
    }

    if (!empty($this->focusId)) {
      new HTMLScript($parentElement,"document.getElementById(\"" . $this->focusId . "\").focus();");
    }
  }

  function buildResetForm($parentElement) {
    $this->user->setInputNames('','','reset_password','reset_ver_password');
    $this->user->setFromPost();
    $userInputs = new UserInputs($this->user);
    $formRecover = new HTMLForm($parentElement,'recover.php','reset_password');
    new HTMLInputHidden($formRecover,'reset',$this->user->getUserId());
    new HTMLText($formRecover,'Enter the new password here.');
    $tableRecover = new Table($formRecover,2,2,'recover_email');
    $userInputs->inputPassword($tableRecover->cells[0][1],$tableRecover->cells[0][0]);
    $userInputs->inputVerPassword($tableRecover->cells[1][1],$tableRecover->cells[1][0]);
    new HTMLInputSubmit($formRecover,'reset_password_submit','Reset');
    return $formRecover;
  }

  function buildRecoveryForm($parentElement) {
    $this->user->setInputNames('recover_email','recover_email');
    $this->user->setFromPost();
    $userInputs = new UserInputs($this->user);
    $FormRecover = new HTMLForm($parentElement,'recover.php','recover_email');
    new HTMLInputHidden($FormRecover,'recover','1');
    new HTMLText($FormRecover,'If you know the email or user name that was last set then enter it here.');
    $TableRecover = new Table($FormRecover,1,2,'recover_email');
    $userInputs->inputEmail($TableRecover->cells[0][1],$TableRecover->cells[0][0],'Email/User Name:');
    new HTMLInputSubmit($FormRecover,'recover_email_submit','Recover');
  }

  function sendRecoveryEmail() {
    $email = new EmailDocument($this->siteInfo,$this->user,"eContriver - Account Restoration");

    $h4SubTitle = new HTMLHeading($email->content,4,"To recover your account follow the instructions below.");
    $h4SubTitle->setAttribute('style','color:gray;');
    new HTMLParagraph($email->content,'Someone has requested that your account password be reset.
    If you did not request this, then discard this email. If you think someone is trying to obtain
    unauthorized access to your account then please send an email to: webmaster@econtriver.com');
    new HTMLParagraph($email->content,'This link will take you to the account recovery page:');

    $cipher = new Cipher($this->siteInfo->getSalt());
    $verify2 = $cipher->encrypt($this->user->getTime());
    $recoveryLink = $this->siteInfo->getSiteHTTP().'/recover.php?recover_id='.$this->user->getUserId().'&verify1='.$this->user->getPassword().'&verify2='.$verify2;
    //echo "<a href='".$recoveryLink."' >RECOVER NOW</a>\n";
    new HTMLAnchor($email->content,$recoveryLink,'Recover My Account');
    new HTMLParagraph($email->content,'Once you follow this link, then enter and verify a new password.  This new password will then be used to login.');

    $sendEmail = new SendEmail();
    $sendEmail->addTo($this->user->getEmail());
    $sendEmail->setFrom($this->siteInfo->getFromEmail());
    $sendEmail->setSubject("eContriver - Account Recovery");
    $sendEmail->setContent($email->printPage());
    return $sendEmail->send();
  }

  function verifyRecoveryLink($parentElement,$recoverId,$verify1,$verify2) {
    if (!$this->user->getUserById($recoverId)) { // Check if User ID exists
      $this->infoMsg->addMessage(0,'The user verification information is incorrect. Resubmit the information.');
      return false;
    } else {
      $this->user->setUserById($recoverId);
      if ($verify1 != $this->user->getPassword()) { // Check if verify1 = user password
        $this->infoMsg->addMessage(0,'The user verification information is incorrect. Resubmit the information.');
        return false;
      } else {
        $cipher = new Cipher($this->siteInfo->getSalt());
        $verify2Decrypted = $cipher->decrypt($verify2);
        $verify2Decrypted += 3600*24*5;
        if ($this->user->getTime() > $verify2Decrypted) { // Check that the link is not older than 5 days
          $this->infoMsg->addMessage(0,'This link is no longer valid. Resubmit the information.');
          return false;
        } else {
          return true;
        }
      }
    }
  }

}