<?php
class HTMLAnchor {
  private $html_anchor_str;
  private $anchor_href_str;
  private $anchor_id_str;
  private $anchor_name_str;
  private $anchor_target_str;
  private $anchor_inner_html_str;

  public function __construct ($anchor_inner_html_str='',$anchor_href_str='',$anchor_name_str='',$anchor_target_str='') {
    $this->setHref($anchor_href_str);
    $this->setId($anchor_name_str.'_anchor');
    $this->setName($anchor_name_str);
    $this->setTarget($anchor_target_str);
    $this->setInnerHTML($anchor_inner_html_str);
    $this->setHTMLAnchor();
  }

  public function setHref($anchor_href_str) {
    $this->anchor_href_str = $anchor_href_str;
  }

  public function setId($anchor_id_str) {
    $this->anchor_id_str = $anchor_id_str;
  }

  public function setName($anchor_name_str) {
    $this->anchor_name_str = $anchor_name_str;
  }

  public function setTarget($anchor_target_str) {
    $this->anchor_target_str = $anchor_target_str;
  }
  
  public function setInnerHTML($anchor_inner_html_str) {
    $this->anchor_inner_html_str = $anchor_inner_html_str;
  }

  public function setHTMLAnchor () {
    $this->html_anchor_str = "<a href=\"$this->anchor_href_str\" id=\"$this->anchor_id_str\" name=\"$this->anchor_name_str\"";
    if (!empty($this->anchor_target_str)) { $this->html_anchor_str .= " target=\"$this->anchor_target_str\""; }
    $this->html_anchor_str .= ">$this->anchor_inner_html_str</a>\n";
    return $this->getHTMLAnchor();
  }

  public function getHTMLAnchor () {
    return $this->html_anchor_str;
  }

  public function __toString() {
    return $this->getHTMLAnchor();
  }
}
?>