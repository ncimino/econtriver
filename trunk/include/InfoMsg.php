<?php
class InfoMsg {
	private $messages = array();
	private $divName;
	public $parentElement;

	public function __construct() {
	}

	public function addMessage($level,$message,$linkText=NULL,$link=NULL) {
		$newIndex = count($this->messages);
		$this->messages[$newIndex]['level'] = $level;
		$this->messages[$newIndex]['message'] = $message;
		if(!empty($linkText)) { $this->messages[$newIndex]['link_text'] = $linkText; }
		if(!empty($link)) { $this->messages[$newIndex]['link'] = $link; }
	}

	public function commitDiv($parentElement) {
		$this->parentElement = $parentElement;
		$this->parentElement->setAttribute('style',"display:none;");
		$this->parentElement->setAttribute('onclick',"hideElement('{$this->parentElement->getId()}')");
		$this->parentElement->setAttribute('title',"Click to hide");
	}

	public function commitMessages() {
		if (count($this->messages) > 0 ) {
			foreach ($this->messages as $index=>$value) {
				if ($index != 0) { new HTMLBr($this->parentElement); }
				if ($value['level']==-1) {
					new HTMLText($this->parentElement,"This should not have occurred. Please report this problem: ");
					new HTMLAnchor($this->parentElement,'bugs.php','Report Bug');
					new HTMLBr($this->parentElement);
					new HTMLSpan($this->parentElement,'Fatal Error: ','','error');
				} elseif ($value['level']==0) {
					new HTMLSpan($this->parentElement,'Error: ','','error');
				} elseif ($value['level']==1) {
					new HTMLSpan($this->parentElement,'Warning: ','','warning');
				} elseif ($value['level']==2) {
					new HTMLSpan($this->parentElement,'Info: ','','info');
				}
				new HTMLText($this->parentElement,$value['message']." ");
				if(!empty($value['link_text'])) { new HTMLAnchor($this->parentElement,$value['link'],$value['link_text']); }
			}
			$this->parentElement->setAttribute('style',"display:block;");
		}
	}

}