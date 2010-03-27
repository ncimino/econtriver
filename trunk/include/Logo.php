<?php
class Logo {
	function __construct($parentElement,$SiteInfo) {
		$DivLogo = new HTMLDiv($parentElement,'logo');
		$H1Logo = new HTMLHeading($DivLogo,1);
		$AnchorLogo = new HTMLAnchor($H1Logo,$SiteInfo->getSiteHTTP(),'','','logo_anchor');
		$ImgLogo = new HTMLImage($AnchorLogo,$SiteInfo->getLogo(),$SiteInfo->getName(),$SiteInfo->getLogoWidth(),$SiteInfo->getLogoHeight(),'logo');
		//$DivBannerShadow = new HTMLDiv($DivLogo,'banner_shadow');
		//$DivBannerShadow->setAttribute('style','width:'.$SiteInfo->getLogoWidth().';height:'.$SiteInfo->getLogoHeight().';');
		$DivBannerSiteName = new HTMLDiv($DivLogo,'banner_site_name');
		//$AnchorSiteName = new HTMLAnchor($DivBannerSiteName,$SiteInfo->getSiteHTTP(),strtolower($SiteInfo->getSubname()),'','sitename_anchor');
	}
}
?>