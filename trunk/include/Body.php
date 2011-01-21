<?php
class Body {
	public $HTML_Body;
	public $divMid;
	public $login;
	public $title;

	function __construct($HTML_Document,$infoMsg,$siteInfo,$user,$title) {
		$this->HTML_Body = new HTML_Body($HTML_Document);
		$divPage = new HTML_Div($this->HTML_Body,'page','page');

		$divInfoMsg = new HTML_Div($divPage,'top_msgs','info_msgs');
		$infoMsg->commitDiv($divInfoMsg);

		$divBanner = new HTML_Div($divPage,'banner','banner');

		$this->login = new Login($divBanner,$siteInfo,$user);
		new Logo($divBanner,$siteInfo);

		$this->divMid = new HTML_Div($divPage,'mid','mid');
		$this->title = new HTML_Heading($this->divMid,3,$title);

		$this->divFooter = new HTML_Div($divPage,'footer','footer');
		$scriptGoogleAnalyticsSet = "var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");
document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));";
		new HTML_Script($this->divFooter,$scriptGoogleAnalyticsSet,null,false);
		$scriptGoogleAnalyticsRun = "try {
var pageTracker = _gat._getTracker(\"UA-2516211-1\");
pageTracker._trackPageview();
} catch(err) {}";
		new HTML_Script($this->divFooter,$scriptGoogleAnalyticsRun,null,false);
	}
}
?>