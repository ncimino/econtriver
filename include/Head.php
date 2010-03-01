<?php
class Head {
  public $HTMLHead;
  function __construct($HTMLDocument,$siteInfo) {
    $this->HTMLHead = new HTMLHead($HTMLDocument);
    new HTMLTitle($this->HTMLHead,$siteInfo->getName());
    new HTMLShortcutIcon($this->HTMLHead,$siteInfo->getIcon(),$siteInfo->getIconType());
    new HTMLKeywords($this->HTMLHead,$siteInfo->getKeywords());
    new HTMLDescription($this->HTMLHead,$siteInfo->getDescription());
    new HTMLStylesheet($this->HTMLHead,$siteInfo->getCss());
    new HTMLScript($this->HTMLHead,'',$siteInfo->getJs());
  }
}
?>