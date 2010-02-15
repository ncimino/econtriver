<?php
class HTMLHeader {
  private $html_header_arr;
  private $html_title_str = "eContriver";
  private $html_icon_file_str = "./images/icon_16.png";
  private $html_icon_type_str = "image/png";
  private $html_keywords_str = "econtriver,contriver,accounting,registry,checkbook,check,balance,savings,saving,checking,loans,loan,tracking,log,shared,accounts,account,investments,track,monitor,watch,";
  private $html_description_str = "Account, investment, loan, project, and cash transaction tracking and management site with ability to share and log all account modifications";
  private $html_css_files_arr = array("./css/main.css");
  private $html_js_files_arr = array("./js/main.js");
  private $html_local_css_arr;
  private $html_local_js_arr;

  public function __construct($html_title_str) {
    if (!empty($html_title_str)) {
      $this->html_title_str = $html_title_str . " - " . $this->html_title_str;
    }
    $css_lte_ie_6 = "div.banner_shadow {
    display: none;
}\n";
    $this->setHTMLLocalCSS($css_lte_ie_6);
    $this->setHTMLHeader();
  }

  public function setHTMLTitle($html_title_str) {
    $this->html_title_str=$html_title_str;
  }

  public function addHTMLKeywords($html_keywords_str) {
    $this->html_keywords_str=$this->html_keywords_str.$html_keywords_str.",";
  }

  public function setHTMLKeywords($html_keywords_str) {
    $this->html_keywords_str=$html_keywords_str;
  }

  public function addHTMLCSSFile($html_css_files_str) {
    $this->html_css_files_arr[count($this->html_css_files_arr)]=$html_css_files_str;
  }

  public function setHTMLCSSFile($html_css_files_str) {
    unset($this->html_css_files_arr);
    if(is_array($form_element_arr)) {
      $this->html_css_files_arr = $html_css_files_str;
    } else {
      $this->html_css_files_arr[] = $html_css_files_str;
    }
  }

  public function addHTMLJSFile ($html_js_files_str) {
    $this->html_js_files_arr[count($this->html_js_files_arr)]=$html_js_files_str;
  }

  public function setHTMLJSFile($html_js_files_str) {
    unset($this->html_js_files_arr);
    if(is_array($html_js_files_str)) {
      $this->html_js_files_arr = $html_js_files_str;
    } else {
      $this->html_js_files_arr[] = $html_js_files_str;
    }
  }

  public function addHTMLLocalJS($html_local_js_str) {
    $this->html_local_js_arr[count($this->html_local_js_arr)]=$html_local_js_str;
  }

  public function setHTMLLocalJS($html_local_js_str) {
    unset($this->html_local_js_arr);
    $this->html_local_js_arr[0]=$html_local_js_str;
  }

  public function addHTMLLocalCSS($html_local_css_str) {
    $this->html_local_css_arr[count($this->html_local_css_arr)]=$html_local_css_str;
  }

  public function setHTMLLocalCSS($html_local_css_str) {
    unset($this->html_local_css_arr);
    $this->html_local_css_arr[0]=$html_local_css_str;
  }

  public function setHTMLHeader() {
    $this->html_header_arr[] = "<title>".$this->html_title_str."</title>\n";
    $this->html_header_arr[] = "<link rel=\"shortcut icon\" href=\"".$this->html_icon_file_str."\" type=\"".$this->html_icon_type_str."\" />\n";
    $this->html_header_arr[] = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\" />\n";
    $this->html_header_arr[] = "<meta name=\"Keywords\" content=\"".$this->html_keywords_str."\" />\n";
    $this->html_header_arr[] = "<meta name=\"Description\" content=\"".$this->html_description_str."\" />\n";

    foreach ($this->html_css_files_arr as $index => $variable) {
      $this->html_header_arr[] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$variable."\" />\n";
    }

    foreach ($this->html_js_files_arr as $index => $variable) {
      $this->html_header_arr[] = "<script type=\"text/javascript\" src=\"".$variable."\"></script>\n";
    }

    //$html_js_arr = array();
    if(is_array($this->html_local_js_arr))
    foreach ($this->html_local_js_arr as $index => $variable) {
      $this->html_header_arr[] = "<script type=\"text/javascript\">\n";
      $this->html_header_arr[] = $variable;
      $this->html_header_arr[] = "</script>\n";
    }
    //if(is_array($html_js_arr))
    //$this->html_header_arr[] = $html_js_arr;

    //$html_css_arr = array();
    if(is_array($this->html_local_css_arr))
    foreach ($this->html_local_css_arr as $index => $variable) {
      $this->html_header_arr[] = "<!--[if lte IE 6]>\n";
      $this->html_header_arr[] = "<style>\n";
      $this->html_header_arr[] = $variable;
      $this->html_header_arr[] = "</style>\n";
      $this->html_header_arr[] = "<![endif]-->\n";
    }
    //if(is_array($html_css_arr))
    //$this->html_header_arr[] = $html_css_arr;
  }

  public function getHTMLHeader() {
    return $this->html_header_arr;
  }

  public function __toString() {
    foreach($this->getHTMLHeader() as $html_header_element)
    $html_header_str .= $html_header_element;
    return $html_header_str;
  }
}