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
				if ($index != 0) { new HTML_Br($this->parentElement); }
				if ($value['level']==-1) {
					$this->parentElement->setAttribute('class',"ui-state-error ui-corner-all");
					new HTML_Text($this->parentElement,"This should not have occurred. Please report this problem: ");
					new HTML_Anchor($this->parentElement,'bugs.php','Report Bug');
					new HTML_Br($this->parentElement);
					$icon = new HTML_Span($this->parentElement,'','','ui-icon ui-icon-alert');
					$icon->setAttribute('style','float: left; margin-right: .3em;');
					new HTML_Strong($this->parentElement,' Fatal Error: ');
				} elseif ($value['level']==0) {
					$this->parentElement->setAttribute('class',"ui-state-error ui-corner-all");
					$icon = new HTML_Span($this->parentElement,'','','ui-icon ui-icon-alert');
					$icon->setAttribute('style','float: left; margin-right: .3em;');
					new HTML_Strong($this->parentElement,' Error: ');
				} elseif ($value['level']==1) {
					$this->parentElement->setAttribute('class',"ui-state-highlight ui-corner-all");
					$icon = new HTML_Span($this->parentElement,'','','ui-icon ui-icon-info');
					$icon->setAttribute('style','float: left; margin-right: .3em;');
					new HTML_Strong($this->parentElement,' Warning: ');
				} elseif ($value['level']==2) {
					$this->parentElement->setAttribute('class',"ui-state-highlight ui-corner-all");
					$icon = new HTML_Span($this->parentElement,'','','ui-icon ui-icon-info');
					$icon->setAttribute('style','float: left; margin-right: .3em;');
					new HTML_Strong($this->parentElement,' Info: ');
				}
				new HTML_Text($this->parentElement,$value['message']." ");
				if(!empty($value['link_text'])) { new HTML_Anchor($this->parentElement,$value['link'],$value['link_text']); }
			}
			$this->parentElement->setAttribute('style',"display:block;");
		}
	}

}