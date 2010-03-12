<?php
class InfoMsg {
  private $messages = array();
  private $body;
  public $divInfoMsg;

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
    $this->divInfoMsg = new HTMLDiv($parentElement,$name);
    $this->divInfoMsg->setAttribute('onclick',"new Effect.BlindUp('{$this->divInfoMsg->getId()}')");
  }

  public function commitMessages() {
    if (count($this->messages) > 0 ) {
      foreach ($this->messages as $index=>$value) {
        if ($index != 0) { new HTMLBr($this->divInfoMsg); }
        if ($value['level']==-1) {
          new HTMLText($this->divInfoMsg,"This should not have occurred. Please report this problem: ");
          new HTMLAnchor($this->divInfoMsg,'bugs.php','Report Bug');
          new HTMLBr($this->divInfoMsg);
          new HTMLSpan($this->divInfoMsg,'Fatal Error: ','error');
        } elseif ($value['level']==0) {
          new HTMLSpan($this->divInfoMsg,'Error: ','error');
        } elseif ($value['level']==1) {
          new HTMLSpan($this->divInfoMsg,'Warning: ','warning');
        } elseif ($value['level']==2) {
          new HTMLSpan($this->divInfoMsg,'Info: ','info');
        }
        new HTMLText($this->divInfoMsg,$value['message']." ");
        if(!empty($value['link_text'])) { new HTMLAnchor($this->divInfoMsg,$value['link'],$value['link_text']); }
        $this->body->setAttribute('onload',"timedHide(10000,'{$this->divInfoMsg->getId()}')");
      }
    } else {
      $this->divInfoMsg->remove();
    }
  }
  
  public function setBody($body) { $this->body = $body; }
}