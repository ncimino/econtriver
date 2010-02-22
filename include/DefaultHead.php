<?php
class DefaultHead {
  public $HTMLHead;
  function __construct($HTMLDocument,$SiteInfo) {
    $this->HTMLHead = new HTMLHead($HTMLDocument);
    new HTMLTitle($this->HTMLHead,$SiteInfo->getName());
    new HTMLShortcutIcon($this->HTMLHead,$SiteInfo->getIcon(),$SiteInfo->getIconType());
    new HTMLKeywords($this->HTMLHead,$SiteInfo->getKeywords());
    new HTMLDescription($this->HTMLHead,$SiteInfo->getDescription());
    new HTMLStylesheet($this->HTMLHead,$SiteInfo->getCss());
    new HTMLScript($this->HTMLHead,'',$SiteInfo->getJs());
    $style = "div.banner_shadow {
    display: none;
}";
    new HTMLStyle($this->HTMLHead,$style,'lte','6');
  }
}
?>