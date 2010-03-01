<?php
class EmailDocument {
  private $document;
  private $siteInfo;
  private $infoMsg;
  private $user;
  public $content;

  function __construct($siteInfo,$user,$title) {
    $this->siteInfo = $siteInfo;
    $this->user = $user;
    $this->document = HTMLDocument::createHTMLDocument();

    $HTMLHead = new HTMLHead($this->document);
    new HTMLTitle($HTMLHead,$siteInfo->getName());
    new HTMLShortcutIcon($HTMLHead,$siteInfo->getIcon(),$siteInfo->getIconType());
    new HTMLKeywords($HTMLHead,$siteInfo->getKeywords());
    new HTMLDescription($HTMLHead,$siteInfo->getDescription());
    new HTMLStylesheet($HTMLHead,$siteInfo->getCss());
    new HTMLScript($HTMLHead,'',$siteInfo->getJs());

    $HTMLBody = new HTMLBody($this->document);
    $DivPage = new HTMLDiv($HTMLBody,'page');
    $DivIELimiter = new HTMLDiv($DivPage,'banner_ie_limiter');
    $DivBanner = new HTMLDiv($DivPage,'banner');
    $Logo = new Logo($DivBanner,$siteInfo);
    $DivMid = new HTMLDiv($DivPage,'mid');
    $H3Title = new HTMLHeading($DivMid,3,$title);

    $this->content = $DivMid;
  }

  function printPage () {
    return $this->document->saveXML();
  }
}
?>