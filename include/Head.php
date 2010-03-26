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
    foreach ($siteInfo->getJs() as $jsFile) {
      new HTMLScript($this->HTMLHead,'',$jsFile);
    }
    /*
     * Script-aculo-us
     */
    // These are killing IE
    //new HTMLScript($this->HTMLHead,'',$siteInfo->getPrototype());
    //new HTMLScript($this->HTMLHead,'',$siteInfo->getScriptaculous());
    /*
     * JQuery
     */
    //new HTMLScript($this->HTMLHead,'',$siteInfo->getSiteHTTP().$siteInfo->getJsDir().'/jquery.js');
    
    //new HTMLScript($this->HTMLHead,'',$siteInfo->getSiteHTTP().$siteInfo->getJsDir().'/jquery-1.4.2.min.js');
    //new HTMLScript($this->HTMLHead,'',$siteInfo->getSiteHTTP().$siteInfo->getJsDir().'/jquery-ui-1.8.custom.min.js');
    
    //*
    new HTMLScript($this->HTMLHead,'',$siteInfo->getSiteHTTP().$siteInfo->getJsDir().'/jquery-1.4.2.js',FALSE);
    new HTMLScript($this->HTMLHead,'',$siteInfo->getSiteHTTP().$siteInfo->getJsDir().'/ui/jquery.ui.core.js',FALSE);
    new HTMLScript($this->HTMLHead,'',$siteInfo->getSiteHTTP().$siteInfo->getJsDir().'/ui/jquery.ui.widget.js',FALSE);
    new HTMLScript($this->HTMLHead,'',$siteInfo->getSiteHTTP().$siteInfo->getJsDir().'/ui/jquery.ui.mouse.js',FALSE);
    new HTMLScript($this->HTMLHead,'',$siteInfo->getSiteHTTP().$siteInfo->getJsDir().'/ui/jquery.ui.draggable.js',FALSE);
    new HTMLScript($this->HTMLHead,'',$siteInfo->getSiteHTTP().$siteInfo->getJsDir().'/ui/jquery.ui.droppable.js',FALSE);//*/
    
    /*
    $script ='
	$(function InitiateDragAndDrop {() {

		$("#mid_div").draggable({ revert: \'invalid\' });

		$("input.acct_name").droppable({
			activeClass: \'ui-state-hover\',
			hoverClass: \'ui-state-active\',
			drop: function(event, ui) {
				$(this).addClass(\'ui-state-highlight\').find(\'p\').html(\'Dropped!\');
			}
		});

	});';
    new HTMLScript($this->HTMLHead,$script,'',FALSE);//*/
  }
}
?>