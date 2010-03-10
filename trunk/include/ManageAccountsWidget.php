<?php
class ManageAccountsWidget {
  public $parentElement;
  public $DB;
  public $siteinfo;

  const createAccDiv = 'create_account';
  const createAccForm = 'create_account';
  const createAccText = 'account_name';
  const createAccButton = 'account_button';

  function __construct($parentElement,$DB,$siteInfo) {
    $this->parentElement = $parentElement;
    $this->DB = $DB;
    $this->siteInfo = $siteInfo;

    $this->updateDB();
    $this->getData();
    $this->buildWidget();
  }

  function updateDB() {
    if (!empty($_POST[self::createAccText]) and $this->siteInfo->verifyReferer()) {
      $accountName = mysql_real_escape_string($_POST[self::createAccText]);
      $sql = "INSERT INTO acct (txn_type_name)
VALUES ('Transfer');";
      $this->DB->query($sql);
      echo "COMPLETED:<BR>\n".$sql."<BR>\n";
    }
  }

  function getData() {

  }

  function buildWidget() {
    $this->addCreateAccountForm();
  }

  function addCreateAccountForm() {
    $divAddAccount = new HTMLDiv($this->parentElement,self::createAccDiv);
    $formAddAccount = new HTMLForm($divAddAccount,$this->siteInfo->getSelfFileName(),self::createAccForm);
    new HTMLInputText($formAddAccount,self::createAccText);
    //new HTMLInputButton($formAddAccount,self::createAccButton,'Add Account');
    new HTMLInputSubmit($formAddAccount,self::createAccButton,'Add Account');
  }
}
?>