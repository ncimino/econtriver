<?php
class HTMLDoc {
  private $html_header_str;
  private $html_body_str;
  private $html_doc_str;
  
  public function __construct ($html_header_str="",$html_body_str="") {
    $this->html_header_str = $html_header_str;
    $this->html_body_str = $html_body_str;
    $this->setHTMLDoc();
  }
  
  public function setHTMLHeader ($html_header_str="") {
    $this->html_header_str = $html_header_str;
  }
  
  public function setHTMLBody ($html_body_str="") {
    $this->html_body_str = $html_body_str;
  }
  
  public function setHTMLDoc () {
    $this->html_doc_str = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n"; 
    $this->html_doc_str .= "<html lang=\"en-US\" xml:lang=\"en-US\" xmlns=\"http://www.w3.org/1999/xhtml\">\n"; 
    $this->html_doc_str .= "<head>\n";
	$this->html_doc_str .= $this->html_header_str;
	$this->html_doc_str .= "</head>\n";
	$this->html_doc_str .= "<body>\n\n";
	$this->html_doc_str .= $this->html_body_str;
	$this->html_doc_str .= "\n</body>\n";
	$this->html_doc_str .= "</html>\n";
  }
  
  public function getHTMLHeader () {
    return $this->html_header_str;
  }
  
  public function getHTMLBody () {
    return $this->html_body_str;
  }
  
  public function getHTMLDoc () {
    return $this->html_doc_str;
  }
}
?>