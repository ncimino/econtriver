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

    $HTMLBody = new HTMLBody($this->document);
    $divPage = new HTMLDiv($HTMLBody,'page');
    $divPage->setAttribute('style','padding:3px;margin:3px;');

    $divBanner = new HTMLDiv($divPage,'banner');
    $divBanner->setAttribute('style','text-align:right;height:50px;');

    $divLogo = new HTMLDiv($divBanner,'logo');
    $divLogo->setAttribute('style','text-align:right;position:absolute;top:0px;right:0px;');
    $h1Logo = new HTMLHeading($divLogo,1);
    $anchorLogo = new HTMLAnchor($h1Logo,$siteInfo->getSiteHTTP(),'','','logo_anchor');
    $logo = new HTMLImage($anchorLogo,$siteInfo->getLogo(),$siteInfo->getName(),$siteInfo->getLogoWidth(),$siteInfo->getLogoHeight(),'logo');
    //$logo->setAttribute('style','border: #DD0 1px solid;');
    
    $divMid = new HTMLDiv($divPage,'mid');
    $divTitle = new HTMLDiv($divMid,'title');
    $divTitle->setAttribute('style','border: #DD0 1px solid;background-color: #FFC;margin:0px;padding:0px;');
    $h3Title = new HTMLHeading($divTitle,3,$title);
    $h3Title->setAttribute('style','padding:0px;margin:2px;color:gray;');
    
    new HTMLBr($divMid);
    
    $this->content = $divMid;
    
    $divFooter = new HTMLDiv($divPage,'footer');
    new HTMLBr($divFooter);
    new HTMLHr($divFooter);
    new HTMLText($divFooter,'Email sent from ');
    new HTMLAnchor($divFooter,$siteInfo->getSiteHTTP(),$siteInfo->getName());
  }

  function printPage () {
    return $this->document->saveXML();
  }
}
?>