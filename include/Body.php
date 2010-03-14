<?php
class Body {
  public $HTMLBody;
  public $divMid;
  public $login;
  public $title;
  
  function __construct($HTMLDocument,$infoMsg,$siteInfo,$user,$title) {
    $this->HTMLBody = new HTMLBody($HTMLDocument);
    $divPage = new HTMLDiv($this->HTMLBody,'page');

    new HTMLDiv($divPage,'banner_ie_limiter');
    $divBanner = new HTMLDiv($divPage,'banner');
    
    $this->login = new Login($divBanner,$siteInfo,$user);
    new Logo($divBanner,$siteInfo);

    $divInfoMsg = new HTMLDiv($divPage,'info_messages');
    $infoMsg->commitDiv($divInfoMsg);
    
    $this->divMid = new HTMLDiv($divPage,'mid');
    $this->title = new HTMLHeading($this->divMid,3,$title);
    
    $this->divFooter = new HTMLDiv($divPage,'footer');
    $scriptGoogleAnalyticsSet = "var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");
document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));";
    new HTMLScript($this->divFooter,$scriptGoogleAnalyticsSet,null,false);
    $scriptGoogleAnalyticsRun = "try {
var pageTracker = _gat._getTracker(\"UA-2516211-1\");
pageTracker._trackPageview();
} catch(err) {}";
    new HTMLScript($this->divFooter,$scriptGoogleAnalyticsRun,null,false);
  }
}
?>