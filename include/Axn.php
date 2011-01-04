<?php 
class Axn {
	public $parentElement;
	public $innerHtml;
	public $action;
	public $axnClass;
	public $id;
	public $linkId;

	protected $uid = '';
	protected $anchor;
	protected $link;
	protected $href = '#';
	protected $axn_arr;
	
	const N_VERIFY_DELETE = 'ver_del';
	
	function __construct($parentElement,$innerHtml,$action,$id,$axnClass='axn',$linkId='') {
		$this->parentElement = $parentElement;
		$this->innerHtml = $innerHtml;
		$this->action = $action;
		$this->axnClass = $axnClass;
		$this->id = $id;
		$this->linkId = $linkId;
		$this->setId();
		$this->setAnchor();
		$this->setLink();
	}
	
	function setAnchor() {
		$this->anchor = new HTML_Anchor($this->parentElement,$this->href,$this->innerHtml,$this->uid,$this->axnClass);
	}
	
	function setLink() {
		$this->link = new HTML_InputHidden($this->parentElement,$this->axnClass,$this->action,$this->linkId,$this->uid);
	}
	
	function setId() {
		$this->uid = $this->action . '_' . $this->id;
	}
	
	function getId() {
		return $this->uid;
	}
	
	function verifyDelete($item) {
		$this->axn_arr[] = new HTML_InputHidden($this->parentElement,self::N_VERIFY_DELETE,$item,'',$this->uid);
	}
	
	function uses($elements) {
		foreach ( $elements as $element ) {
			$element->setClass(self::getId().' '.$element->getClass());
		}
	}
	
	function remove() {
		$this->anchor->remove();
		$this->link->remove();
		foreach ( $this->axn_arr as $value ) {
			$value->remove();
		}
	}
}
?>