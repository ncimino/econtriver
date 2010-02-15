<?php
class HTMLDoc {
  private $html_doc_str;
  private $html_header_arr;
  private $html_body_arr;
  
  public function __construct($html_header_arr,$html_body_arr) {
    $this->html_header_arr = $html_header_arr;
    $this->html_body_arr = $html_body_arr;
    $this->setHTMLDoc();
  }
  
  public function setHTMLHeader($html_header_arr="") {
    $this->html_header_arr = $html_header_arr;
  }
  
  public function setHTMLBody($html_body_arr="") {
    $this->html_body_arr = $html_body_arr;
  }
  
  public function setHTMLDoc() {
    $this->html_doc_str .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n"; 
    $this->html_doc_str .= "<html lang=\"en-US\" xml:lang=\"en-US\" xmlns=\"http://www.w3.org/1999/xhtml\">\n"; 
    $this->html_doc_str .= "<head>\n";
    foreach ($this->html_header_arr as $header_str)
    $this->html_doc_str .= $header_str;
	$this->html_doc_str .= "</head>\n";
	$this->html_doc_str .= "<body>\n";
	$this->html_doc_str .= "<div class=\"page\" >\n";
	foreach ($this->html_body_arr as $body_str)
	$this->html_doc_str .= $body_str;
	$this->html_doc_str .= "</div>\n";
	$this->html_doc_str .= "</body>\n";
	$this->html_doc_str .= "</html>\n";
  }
  
  public function getHTMLHeader() {
    return $this->html_header_arr;
  }
  
  public function getHTMLBody() {
    return $this->html_body_arr;
  }
  
  public function getHTMLDoc() {
    return $this->html_doc_str;
  }
  
  public function __toString() {
    return $this->getHTMLDoc();
  }
}
?>