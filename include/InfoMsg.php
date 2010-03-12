<?php
class InfoMsg {
  private $messages = array();
  public $DivInfoMsg;

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
    $name = 'info_messages';
    $this->DivInfoMsg = new HTMLDiv($parentElement,$name);
    $this->DivInfoMsg->setAttribute('onclick',"new Effect.BlindUp('{$this->DivInfoMsg->getId()}')");
  }

  public function commitMessages() {
    if (count($this->messages) > 0 ) {
      foreach ($this->messages as $index=>$value) {
        if ($index != 0) { new HTMLBr($this->DivInfoMsg); }
        if ($value['level']==-1) {
          new HTMLText($this->DivInfoMsg,"This should not have occurred. Please report this problem: ");
          new HTMLAnchor($this->DivInfoMsg,'bugs.php','Report Bug');
          new HTMLBr($this->DivInfoMsg);
          new HTMLSpan($this->DivInfoMsg,'Fatal Error: ','error');
        } elseif ($value['level']==0) {
          new HTMLSpan($this->DivInfoMsg,'Error: ','error');
        } elseif ($value['level']==1) {
          new HTMLSpan($this->DivInfoMsg,'Warning: ','warning');
        } elseif ($value['level']==2) {
          new HTMLSpan($this->DivInfoMsg,'Info: ','info');
        }
        new HTMLText($this->DivInfoMsg,$value['message']." ");
        if(!empty($value['link_text'])) { new HTMLAnchor($this->DivInfoMsg,$value['link'],$value['link_text']); }
      }
    } else {
      $this->DivInfoMsg->remove();
    }
  }
}