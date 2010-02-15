<?php
class HTMLImg {
  private $html_img_str;
  private $img_class_str;
  private $img_id_str;
  private $img_name_str;
  private $img_alt_str;
  private $img_width_str;
  private $img_height_str;
  private $img_src_str;

  public function __construct ($img_name_str='',$img_alt_str='',$img_width_str='',$img_height_str='',$img_src_str='') {
    $this->setClass($img_name_str);
    $this->setId($img_name_str.'_img');
    $this->setName($img_name_str);
    $this->setAlt($img_alt_str);
    $this->setWidth($img_width_str);
    $this->setHeight($img_height_str);
    $this->setSrc($img_src_str);
    $this->setHTMLImg();
  }

  public function setClass($img_class_str) {
    $this->img_class_str = $img_class_str;
  }

  public function setId($img_id_str) {
    $this->img_id_str = $img_id_str;
  }

  public function setName($img_name_str) {
    $this->img_name_str = $img_name_str;
  }

  public function setAlt($img_alt_str) {
    $this->img_alt_str = $img_alt_str;
  }

  public function setWidth($img_width_str) {
    $this->img_width_str = $img_width_str;
  }

  public function setHeight($img_height_str) {
    $this->img_height_str = $img_height_str;
  }

  public function setSrc($img_src_str) {
    $this->img_src_str = $img_src_str;
  }

  public function setHTMLImg() {
    $this->html_img_str .= "<img src=\"$this->img_src_str\"";
    if (!empty($this->img_class_str)) { $this->html_img_str .= " class=\"$this->img_class_str\""; }
    if (!empty($this->img_id_str)) { $this->html_img_str .= " id=\"$this->img_id_str\""; }
    //if (!empty($this->img_name_str)) { $this->html_img_str .= " name=\"$this->img_name_str\""; }
    if (!empty($this->img_alt_str)) { $this->html_img_str .= " alt=\"{$this->img_alt_str}\""; }
    if (!empty($this->img_width_str)) { $this->html_img_str .= " width=\"{$this->img_width_str}\""; }
    if (!empty($this->img_height_str)) { $this->html_img_str .= " height=\"{$this->img_height_str}\""; }
    $this->html_img_str .= " />";
    return $this->getHTMLImg();
  }

  public function getHTMLImg() {
    return $this->html_img_str;
  }

  public function __toString() {
    return $this->getHTMLImg();
  }
}
?>