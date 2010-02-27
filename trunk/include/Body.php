<?php
class Body {
  public $HTMLBody;
  public $DivMid;
  function __construct($HTMLDocument,$infoMsg,$siteInfo,$user,$title) {
    $this->HTMLBody = new HTMLBody($HTMLDocument);
    $DivPage = new HTMLDiv($this->HTMLBody,'page');

    $infoMsg->commitDiv($DivPage);
    
    $DivIELimiter = new HTMLDiv($DivPage,'banner_ie_limiter');
    $DivBanner = new HTMLDiv($DivPage,'banner');
    
    $Login = new Login($DivBanner,$user);
    $Logo = new Logo($DivBanner,$siteInfo);

    $this->DivMid = new HTMLDiv($DivPage,'mid');
    $H3Title = new HTMLHeading($this->DivMid,3,$title);
  }
}
?>