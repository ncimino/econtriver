<?php
class HTMLInput {
  private $html_input_str;
  private $input_class_str;
  private $input_id_str;
  private $input_label_str;
  private $input_type_str;

  public function __construct ($input_name_str='',$input_label_str='',$input_value_str='',$input_type_str='') {
    $this->setClass($input_name_str);
    $this->setId($input_name_str.'_input');
    $this->setName($input_name_str);
    $this->setValue($input_value_str);
    $this->setLabel($input_label_str);
    $this->setType($input_type_str);
    $this->setHTMLInput();
  }

  public function setClass($input_class_str) {
    $this->input_class_str = $input_class_str;
  }

  public function setId($input_id_str) {
    $this->input_id_str = $input_id_str;
  }

  public function setName($input_name_str) {
    $this->input_name_str = $input_name_str;
  }

  public function setValue($input_value_str) {
    $this->input_value_str = $input_value_str;
  }

  public function setLabel($input_label_str) {
    $this->input_label_str = $input_label_str;
  }

  public function setType($input_type_str) {
    $this->input_type_str = $input_type_str;
  }

  public function setHTMLInput() {
    if (!empty($this->input_label_str)) {
      $this->html_input_str = "<label for=\"$this->input_id_str\">$this->input_label_str</label>\n";
    } else {
      $this->html_input_str = "";
    }
    $this->html_input_str .= "<input type=\"$this->input_type_str\"";
    if (!empty($this->input_class_str)) { $this->html_input_str .= " class=\"$this->input_class_str\""; }
    if (!empty($this->input_id_str)) { $this->html_input_str .= " id=\"$this->input_id_str\""; }
    if (!empty($this->input_name_str)) { $this->html_input_str .= " name=\"$this->input_name_str\""; }
    if (!empty($this->input_value_str)) { $this->html_input_str .= " value=\"{$this->input_value_str}\""; }
    $this->html_input_str .= " />\n";
    return $this->getHTMLInput();
  }

  public function getHTMLInput() {
    return $this->html_input_str;
  }

  public function __toString() {
    return $this->getHTMLInput();
  }
}
?>