<?php
class Body {
  public $HTMLBody;
  public $divMid;
  public $login;
  public $title;
  
  function __construct($HTMLDocument,$infoMsg,$siteInfo,$user,$title) {
    $this->HTMLBody = new HTMLBody($HTMLDocument);
    $divPage = new HTMLDiv($this->HTMLBody,'page');

    $infoMsg->commitDiv($divPage);
    
    new HTMLDiv($divPage,'banner_ie_limiter');
    $divBanner = new HTMLDiv($divPage,'banner');
    
    $this->login = new Login($divBanner,$siteInfo,$user);
    new Logo($divBanner,$siteInfo);

    $this->divMid = new HTMLDiv($divPage,'mid');
    $this->title = new HTMLHeading($this->divMid,3,$title);
  }
}
?>