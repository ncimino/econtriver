<?php 
class Axn {
	public $parentElement;
	public $text;
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
	
	function __construct($parentElement,$text,$action,$id,$axnClass='axn',$linkId='') {
		$this->parentElement = $parentElement;
		$this->text = $text;
		$this->action = $action;
		$this->axnClass = $axnClass;
		$this->id = $id;
		$this->linkId = $linkId;
		$this->setId();
		$this->setAnchor();
		$this->setLink();
	}
	
	function setAnchor() {
		$this->$anchor = new HTML_Anchor($this->parentElement,$this->href,$this->text,$this->uid,$this->axnClass);
	}
	
	function setLink() {
		$this->$link = new HTML_InputHidden($this->parentElement,$this->axnClass,$this->action,$this->linkId,$this->uid);
	}
	
	function setId() {
		$this->uid = $this->action . '_' . $this->id;
	}
	
	function getId() {
		return $this->uid;
	}
}
?>