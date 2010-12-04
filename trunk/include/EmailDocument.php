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
		$this->document = HTML_Document::createHTML_Document();

		$HTML_Head = new HTML_Head($this->document);
		new HTML_Title($HTML_Head,$siteInfo->getName());

		$HTML_Body = new HTML_Body($this->document);
		$divPage = new HTML_Div($HTML_Body,'page');
		$divPage->setAttribute('style','padding:3px;margin:3px;font-family: Verdana, Arial, Helvetica, sans-serif;');

		$divBanner = new HTML_Div($divPage,'banner');
		$divBanner->setAttribute('style','text-align:right;');

		$divLogo = new HTML_Div($divBanner,'logo');
		$divLogo->setAttribute('style','text-align:right;');
		$h1Logo = new HTML_Heading($divLogo,1);
		$h1Logo->setAttribute('style','margin:0px;');
		$anchorLogo = new HTML_Anchor($h1Logo,$siteInfo->getSiteHTTP(),'e','','logo_anchor');
		$anchorLogo->setAttribute('style','text-decoration:none;color:gray;');
		$logo = new HTML_Span($anchorLogo,'Contriver');
		$logo->setAttribute('style','color: #EE9;');

		//$logo = new HTML_Image($anchorLogo,$siteInfo->getLogo(),$siteInfo->getName(),$siteInfo->getLogoWidth(),$siteInfo->getLogoHeight(),'logo');
		//$logo->setAttribute('style','border: #DD0 1px solid;');

		$divMid = new HTML_Div($divPage,'mid');
		$divTitle = new HTML_Div($divMid,'title');
		$divTitle->setAttribute('style','border:#DD0 1px solid;background-color:#FFC;margin:0px;padding:0px;');
		$h3Title = new HTML_Heading($divTitle,3,$title);
		$h3Title->setAttribute('style','padding:0px;margin:2px;color:gray;');

		new HTML_Br($divMid);

		$this->content = $divMid;

		$divFooter = new HTML_Div($divPage,'footer');
		new HTML_Br($divFooter);
		new HTML_Hr($divFooter);
		new HTML_Text($divFooter,'Email sent from ');
		new HTML_Anchor($divFooter,$siteInfo->getSiteHTTP(),$siteInfo->getName());
	}

	function printPage () {
		return $this->document->saveXML();
	}
}
?>