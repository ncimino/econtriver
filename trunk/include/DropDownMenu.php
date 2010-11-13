<?php
class DropDownMenu {
	public $menu;
	public $optgroups = array();
	public $options = array();
	private $num_groups = 0;
	private $num_options = 0;
	private $option_selected = FALSE;
	
	function __construct($parentElement,$name=NULL,$id=NULL,$class=NULL) {
		$this->menu = new HTMLSelect($parentElement,$name,$id,$class);
	}

	function removeOptionClass($option) {
		$this->options[$option]->removeAttribute('class');
	}

	function removeOptionId($option) {
		$this->options[$option]->removeAttribute('id');
	}

	function removeOptionAttribs($option) {
		$this->removeOptionClass($option);
		$this->removeOptionId($option);
	}

	function removeOptGroupClass($group) {
		$this->optgroups[$group]->removeAttribute('class');
	}

	function removeOptGroupId($group) {
		$this->optgroups[$group]->removeAttribute('id');
	}

	function removeOptGroupAttribs($group) {
		$this->removeOptGroupClass($group);
		$this->removeOptGroupId($group);
	}
	
	function addOption($innerHTML,$value=NULL,$selected=FALSE,$id=NULL,$class=NULL) {
		$option = new HTMLOption($this->menu,$innerHTML,$value,$selected,$id,$class);
		$this->option_selected = ($this->option_selected) ? TRUE : $selected;
		$this->options[$this->num_options++] = $option;
		if (!empty($class)) { $option->setClass($class.'_option'); }
		if (!empty($id)) { $option->setId($id.'_'.$this->num_options); }
	}
	
	function addOptGroup($innerHTML,$id=NULL,$class=NULL) {
		$group = new HTMLOptGroup($this->menu,$innerHTML,$selected,$id,$class);
		$this->optgroups[$this->num_groups++] = $group;
		if (!empty($class)) { $group->setClass($class.'_group'); }
		if (!empty($id)) { $group->setId($id.'_'.$this->num_groups); }
	}
	
	function addOptGroupOption($groupElement,$innerHTML,$value=NULL,$selected=FALSE,$id=NULL,$class=NULL) {
		$option = new HTMLOption($groupElement,$innerHTML,$value,$selected,$id,$class);
		$this->option_selected = ($this->option_selected) ? TRUE : $selected;
		$this->options[$this->num_options++] = $option;
		if (!empty($class)) { $option->setClass($class.'_option'); }
		if (!empty($id)) { $option->setId($id.'_'.$this->num_options); }
	}
	
	function disableOption($option = NULL) {
		if ($this->num_options > 0) {
			if ($option == NULL) $option = $this->num_options - 1;
			$this->options[$option]->setAttribute('disabled','disabled');
		}
	}
	
	function setSelected($option) {
		if (($this->option_selected == FALSE) and count($this->options)) {
			$this->options[$option]->setAttribute('selected','selected');
		}
	}
}
?>