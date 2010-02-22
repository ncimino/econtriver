<?php
class DefaultBody {
  public $HTMLBody;
  public $DivMid;
  function __construct($HTMLDocument,$SiteInfo,$User,$title) {
    $this->HTMLBody = new HTMLBody($HTMLDocument);
    $DivPage = new HTMLDiv($this->HTMLBody,'page');
    
    $DivIELimiter = new HTMLDiv($DivPage,'banner_ie_limiter');
    $DivBanner = new HTMLDiv($DivPage,'banner');
    
    $Login = new Login($DivBanner,$SiteInfo,$User);
    $Logo = new Logo($DivBanner,$SiteInfo);

    $this->DivMid = new HTMLDiv($DivPage,'mid');
    $H3Title = new HTMLHeading($this->DivMid,3,$title);
  }
}
?>