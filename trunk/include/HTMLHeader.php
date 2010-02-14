<?php
class HTMLHeader {
  private $html_header_str;
  private $html_title_str;  
  private $html_icon_file_str = "./images/icon_16.png";
  private $html_icon_type_str = "image/png";
  private $html_keywords_str = "econtriver,contriver,accounting,registry,checkbook,check,balance,savings,saving,checking,loans,loan,tracking,log,shared,accounts,account,investments,track,monitor,watch,";
  private $html_description_str = "Account, investment, loan, project, and cash transaction tracking and management site with ability to share and log all account modifications";
  private $html_css_files_arr;
  private $html_js_files_arr;
  private $html_local_css_arr;
  private $html_local_js_arr;
    
  public function __construct ($html_title_str="") {
    $this->html_title_str = $html_title_str;
    $this->html_css_files_arr[0] = "./css/main.css";
    $this->html_js_files_arr[0] = "./js/main.js";
    $this->setHTMLHeader();
  }
  
  public function setHTMLTitle ($html_title_str) {
    $this->html_title_str=$html_title_str;
  }
  
  public function addHTMLKeywords ($html_keywords_str) {
    $this->html_keywords_str=$this->html_keywords_str.$html_keywords_str.",";
  }
  
  public function setHTMLKeywords ($html_keywords_str) {
    $this->html_keywords_str=$html_keywords_str;
  }
  
  public function addHTMLCSSFile ($html_css_files_str) {
    $this->html_css_files_arr[count($this->html_css_files_arr)]=$html_css_files_str;
  }
  
  public function setHTMLCSSFile ($html_css_files_str) {
    unset($this->html_css_files_arr);
    $this->html_css_files_arr[0]=$html_css_files_str;
  }
  
  public function addHTMLJSFile ($html_js_files_str) {
    $this->html_js_files_arr[count($this->html_js_files_arr)]=$html_js_files_str;
  }
  
  public function setHTMLJSFile ($html_js_files_str) {
    unset($this->html_js_files_arr);
    $this->html_js_files_arr[0]=$html_js_files_str;
  }
  
  public function addHTMLLocalJS ($html_local_js_str) {
    $this->html_local_js_arr[count($this->html_local_js_arr)]=$html_local_js_str;
  }
  
  public function setHTMLLocalJS ($html_local_js_str) {
    unset($this->html_local_js_arr);
    $this->html_local_js_arr[0]=$html_local_js_str;
  }
  
  public function addHTMLLocalCSS ($html_local_css_str) {
    $this->html_local_css_arr[count($this->html_local_css_arr)]=$html_local_css_str;
  }
  
  public function setHTMLLocalCSS ($html_local_css_str) {
    unset($this->html_local_css_arr);
    $this->html_local_css_arr[0]=$html_local_css_str;
  }
  
  public function setHTMLHeader () {
    $this->html_header_str = "<title>".$this->html_title_str."</title>\n";
    $this->html_header_str .= "<link rel=\"shortcut icon\" href=\"".$this->html_icon_file_str."\" type=\"".$this->html_icon_type_str."\" />\n"; 
    $this->html_header_str .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\" />\n";
    $this->html_header_str .= "<meta name=\"Keywords\" content=\"".$this->html_keywords_str."\" />\n";
    $this->html_header_str .= "<meta name=\"Description\" content=\"".$this->html_description_str."\" />\n";
    foreach ($this->html_css_files_arr as $index => $variable) {
    	$this->html_header_str .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$variable."\" />\n";
    }
    foreach ($this->html_js_files_arr as $index => $variable) {
    	$this->html_header_str .= "<script type=\"text/javascript\" src=\"".$variable."\"></script>\n";
    }
    if(is_array($this->html_local_js_arr))
    foreach ($this->html_local_js_arr as $index => $variable) {
      $this->html_header_str .= "<script type=\"text/javascript\">\n"; 
      $this->html_header_str .= $variable;
      $this->html_header_str .= "</script>\n";
    }
    if(is_array($this->html_local_css_arr))
    foreach ($this->html_local_css_arr as $index => $variable) {
      $this->html_header_str .= "<style>\n"; 
      $this->html_header_str .= $variable;
      $this->html_header_str .= "</style>\n";
    }
  }
  
  public function getHTMLHeader () {
    return $this->html_header_str;
  }
}