<?php
class UnorderedList {
	public $list;
	public $items = array();
	private $num_items;
	function __construct($parentElement,$num_items,$id=NULL,$class=NULL) {
		$this->list = new HTMLUnorderedList($parentElement,$id,$class);
		for ($this->num_items=0;$this->num_items<$num_items;$this->num_items++) {
			$item = new HTMLListItem($this->list);
			$this->items[$this->num_items] = $item;
			if (!empty($class)) { $item->setClass($class.'_item'); }
			if (!empty($id)) { $item->setId($id.'_'.$this->num_items); }
		}
	}

	function removeItemClass($item) {
		$this->items[$item]->removeAttribute('class');
	}

	function removeItemId($item) {
		$this->items[$item]->removeAttribute('id');
	}

	function removeItemAttribs($item) {
		$this->removeItemClass($item);
		$this->removeItemId($item);
	}

	function addItem($value='') {
		$item = new HTMLListItem($this->list,$value);
		$this->items[$this->num_items++] = $item;
		if (!empty($class)) { $item->setClass($class.'_item'); }
		if (!empty($id)) { $item->setId($id.'_'.$this->num_items); }
	}
}
?>