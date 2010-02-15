<?php
class HTMLLogo {
  private $html_logo_arr;
  private $site_info_obj;

  public function __construct($site_info_obj) {
    $this->site_info_obj = $site_info_obj;
    $this->setHTMLLogo();
  }

  public function setHTMLLogo() {
    $site_info_obj = $this->site_info_obj;
    $domain = $site_info_obj->getINIValue('siteinfo.domain');
    $img = new HTMLImg('logo',$site_info_obj->getINIValue('siteinfo.sitename'),'371px','80px',$domain."images/logo-normal.png");

    $this->html_logo_arr[] =  "<div class=\"logo\">\n";
    $this->html_logo_arr[] =  "<h1>";
    $this->html_logo_arr[] = new HTMLAnchor($img,$domain,'logo','');
    $this->html_logo_arr[] =  "</h1>\n";
    $this->html_logo_arr[] = "<div class=\"banner_shadow\" ></div>\n";
    $this->html_logo_arr[] = "<div class=\"banner_site_name\" >".strtolower($site_info_obj->getINIValue('siteinfo.subsitename'))."</div>\n";
    $this->html_logo_arr[] = "</div>\n";
  }

  public function getHTMLLogo() {
    return $this->html_logo_arr;
  }

  public function __toString() {
    foreach($this->getHTMLLogo() as $html_logo_element)
    $html_logo_str .= $html_logo_element;
    return $html_logo_str;
  }
}
?>