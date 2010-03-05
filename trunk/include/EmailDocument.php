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
    $divPage->setAttribute('style','padding:3px;margin:3px;font-family: Verdana, Arial, Helvetica, sans-serif;');

    $divBanner = new HTMLDiv($divPage,'banner');
    $divBanner->setAttribute('style','text-align:right;');

    $divLogo = new HTMLDiv($divBanner,'logo');
    $divLogo->setAttribute('style','text-align:right;');
    $h1Logo = new HTMLHeading($divLogo,1);
    $h1Logo->setAttribute('style','margin:0px;');
    $anchorLogo = new HTMLAnchor($h1Logo,$siteInfo->getSiteHTTP(),'e','','logo_anchor');
    $anchorLogo->setAttribute('style','text-decoration:none;color:gray;');
    $logo = new HTMLSpan($anchorLogo,'Contriver'); 
    $logo->setAttribute('style','color: #EE9;');
    
    //$logo = new HTMLImage($anchorLogo,$siteInfo->getLogo(),$siteInfo->getName(),$siteInfo->getLogoWidth(),$siteInfo->getLogoHeight(),'logo');
    //$logo->setAttribute('style','border: #DD0 1px solid;');
    
    $divMid = new HTMLDiv($divPage,'mid');
    $divTitle = new HTMLDiv($divMid,'title');
    $divTitle->setAttribute('style','border:#DD0 1px solid;background-color:#FFC;margin:0px;padding:0px;');
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