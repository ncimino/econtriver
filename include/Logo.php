<?php
class Logo {
	function __construct($parentElement,$SiteInfo) {
		$DivLogo = new HTML_Div($parentElement,'logo');
		$H1Logo = new HTML_Heading($DivLogo,1);
		$AnchorLogo = new HTML_Anchor($H1Logo,$SiteInfo->getSiteHTTP(),'','','logo_anchor');
		$ImgLogo = new HTML_Image($AnchorLogo,$SiteInfo->getLogo(),$SiteInfo->getName(),$SiteInfo->getLogoWidth(),$SiteInfo->getLogoHeight(),'logo');
		//$DivBannerShadow = new HTML_Div($DivLogo,'banner_shadow');
		//$DivBannerShadow->setAttribute('style','width:'.$SiteInfo->getLogoWidth().';height:'.$SiteInfo->getLogoHeight().';');
		$DivBannerSiteName = new HTML_Div($DivLogo,'banner_site_name');
		$AnchorSiteName = new HTML_Anchor($DivBannerSiteName,$SiteInfo->getSiteHTTP(),strtolower($SiteInfo->getSubname()),'sitename_anchor');
	}
}
?>