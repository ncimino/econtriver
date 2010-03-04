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
        $this->user->setFromPost('','','reset_password','reset_ver_password');
        $this->buildResetForm($parentElement);
        $this->focusId = $this->user->getPasswordId();
      } else {
        $this->user->setFromPost('recover_email','recover_email');
        $this->buildRecoveryForm($parentElement);
      }
      /*
       if (!$this->user->getUserById($_GET['recover_id'])) { // Check if User ID exists
       $this->infoMsg->addMessage(0,'The user verification information is incorrect. Resubmit the information.');
       $this->user->setFromPost('recover_email','recover_email');
       $recoveryForm = $this->buildRecoveryForm($parentElement);
       } else {
       $this->user->setUserById($_GET['recover_id']);
       if ($_GET['verify1'] != $this->user->getPassword()) { // Check if verify1 = user password
       $this->infoMsg->addMessage(0,'The user verification information is incorrect. Resubmit the information.');
       $this->user->setFromPost('recover_email','recover_email');
       $recoveryForm = $this->buildRecoveryForm($parentElement);
       } else {
       $cipher = new Cipher($this->siteInfo->getSalt());
       $verify2 = $cipher->decrypt($_GET['verify2']);
       $verify2 += 3600*24*5;
       if ($this->user->getTime() > $verify2) { // Check that the link is not older than 5 days
       $this->infoMsg->addMessage(0,'This link is no longer valid. Resubmit the information.');
       $this->user->setFromPost('recover_email','recover_email');
       $recoveryForm = $this->buildRecoveryForm($parentElement);
       } else {
       $this->user->setFromPost('','','reset_password','reset_ver_password');
       $this->buildResetForm($parentElement);
       }
       }
       }*/

    } elseif (isset($_POST['reset'])) { // User has used entered new passwords

      if (!$this->user->getUserById($_POST['reset'])) { // Check if User ID exists
        $this->infoMsg->addMessage(0,'The user verification information is incorrect. Resubmit the information.');
        $this->user->setFromPost('recover_email','recover_email');
        $recoveryForm = $this->buildRecoveryForm($parentElement);
      } else {
        $this->user->setUserById($_POST['reset']);
        $this->user->setPassword($_POST['reset_password']);
        $this->user->setVerPassword($_POST['reset_ver_password']);
        if ($this->user->updateUser()) { // Check if password was valid and updates the user if it was
          new HTMLText($parentElement,'Password reset successful.');
          $this->focusId = NULL;
        } else {
          $this->user->setFromPost('','','reset_password','reset_ver_password');
          $this->buildResetForm($parentElement);
          $this->focusId = $this->user->focusId;
        }
      }

    } elseif ($_POST['recover']=='1') { // User has submitted a user name or email for recovery

      $this->user->setFromPost('recover_email','recover_email');
      $recoveryForm = $this->buildRecoveryForm($parentElement);
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

    } else { // No information has been entered yet

      $this->user->setFromPost('recover_email','recover_email');
      $recoveryForm = $this->buildRecoveryForm($parentElement);

    }

    if (!empty($this->focusId)) {
      new HTMLScript($parentElement,"document.getElementById(\"" . $this->focusId . "\").focus();");
    }
  }

  function buildResetForm($parentElement) {
    $userInputs = new UserInputs($this->user);
    $FormRecover = new HTMLForm($parentElement,'recover.php','reset_password');
    new HTMLInputHidden($FormRecover,'reset',$this->user->getUserId());
    new HTMLText($FormRecover,'Enter the new password here.');
    $TableRecover = new Table($FormRecover,2,2,'recover_email');
    $userInputs->inputPassword($TableRecover->cells[0][1],$TableRecover->cells[0][0]);
    $userInputs->inputVerPassword($TableRecover->cells[1][1],$TableRecover->cells[1][0]);
    new HTMLInputSubmit($FormRecover,'reset_password_submit','Reset');
  }

  function buildRecoveryForm($parentElement) {
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
    new HTMLAnchor($email->content,$this->siteInfo->getSiteHTTP().'/recover.php?recover_id='.$this->user->getUserId().'&verify1='.$this->user->getPassword().'&verify2='.$verify2,'Recover My Account');
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
      //$this->user->setFromPost('recover_email','recover_email');
      //$recoveryForm = $this->buildRecoveryForm($parentElement);
    } else {
      $this->user->setUserById($recoverId);
      if ($verify1 != $this->user->getPassword()) { // Check if verify1 = user password
        $this->infoMsg->addMessage(0,'The user verification information is incorrect. Resubmit the information.');
        return false;
        //$this->user->setFromPost('recover_email','recover_email');
        //$recoveryForm = $this->buildRecoveryForm($parentElement);
      } else {
        $cipher = new Cipher($this->siteInfo->getSalt());
        $verify2Decrypted = $cipher->decrypt($verify2);
        $verify2Decrypted += 3600*24*5;
        if ($this->user->getTime() > $verify2Decrypted) { // Check that the link is not older than 5 days
          $this->infoMsg->addMessage(0,'This link is no longer valid. Resubmit the information.');
          return false;
          //$this->user->setFromPost('recover_email','recover_email');
          //$recoveryForm = $this->buildRecoveryForm($parentElement);
        } else {
          return true;
          //$this->user->setFromPost('','','reset_password','reset_ver_password');
          //$this->buildResetForm($parentElement);
        }
      }
    }
  }

}