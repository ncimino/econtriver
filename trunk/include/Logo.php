<?php
class Logo {
  function __construct($DivBanner,$SiteInfo) {
    $DivLogo = new HTMLDiv($DivBanner,'logo');
    $H1Logo = new HTMLHeading($DivLogo,1);
    $AnchorLogo = new HTMLAnchor($H1Logo,$SiteInfo->getSiteHTTP(),'','','logo_anchor');
    $ImgLogo = new HTMLImage($AnchorLogo,$SiteInfo->getLogo(),$SiteInfo->getName(),$SiteInfo->getLogoWidth(),$SiteInfo->getLogoHeight(),'logo');
    $DivBannerShadow = new HTMLDiv($DivLogo,'banner_shadow');
    $DivBannerSiteName = new HTMLDiv($DivLogo,'banner_site_name');
    $TextHyphen = new HTMLText($DivBannerSiteName,strtolower($SiteInfo->getSubname()));
  }
}
?>