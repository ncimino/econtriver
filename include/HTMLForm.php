<?php
class HTMLForm {
  private $html_form_str;
  private $form_class_str;
  private $form_id_str;
  private $form_method_str;
  private $form_action_str;
  private $form_element_arr = array();

  public function __construct ($form_name_str='',$form_element_arr='') {
    $this->setClass($form_name_str);
    $this->setId($form_name_str.'_form');
    $this->setMethod('post');
    $this->setAction('');
    $this->setElement($form_element_arr);
    $this->setHTMLForm();
  }

  public function setClass($form_class_str) {
    $this->form_class_str = $form_class_str;
  }

  public function setId($form_id_str) {
    $this->form_id_str = $form_id_str;
  }

  public function setMethod($form_method_str) {
    $this->form_method_str = $form_method_str;
  }

  public function setAction($form_action_str) {
    $this->form_action_str = $form_action_str;
  }

  public function setElement($form_element_arr) {
    unset($this->form_element_arr);
    if(is_array($form_element_arr)) {
      $this->form_element_arr = $form_element_arr;
    } else {
      $this->form_element_arr[] = $form_element_arr;
    }
    $this->setHTMLForm();
  }

  public function addElement($form_element_str) {
    $this->form_element_arr[count($this->form_element_arr)]=$form_element_str;
  }

  public function setHTMLForm() {
    $this->html_form_str = "<form";
    if (!empty($this->form_class_str)) { $this->html_form_str .= " class=\"$this->form_class_str\""; }
    if (!empty($this->form_id_str)) { $this->html_form_str .= " id=\"$this->form_id_str\""; }
    $this->html_form_str .= " method=\"{$this->form_method_str}\" action=\"$this->form_action_str\">\n";
    foreach ($this->form_element_arr as $form_element_str)
    $this->html_form_str .= $form_element_str;
    $this->html_form_str .= "</form>\n";
    return $this->getHTMLForm();
  }
  
  public function getFormId() {
    return $this->form_id_str;
  }
  
  public function getHTMLForm() {
    return $this->html_form_str;
  }

  public function __toString() {
    return $this->getHTMLForm();
  }
}
?>