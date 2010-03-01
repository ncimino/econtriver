<?php
class SendEmail {

  private $to = array();
  private $cc = array();
  private $bcc = array();
  private $content = "";
  private $subject = "";
  private $header = array();
  private $attachments = array();
  private $priority = 3;
  private $charSet = "iso-8859-1";
  private $contentType = "text/html";
  private $encoding = "8bit";

  function removeUser($address){
    $vars = array('to', 'cc', 'bcc');
    foreach($vars as $value){
      $key = array_search($address, $this->{$value});
      if(isset($key)){ $this->{$value}[$key] = ""; }
      unset($key);
    }
  }

  function addTo($address){ $this->to[] = $address; }
  function addCC($address){ $this->cc[] = $address; }
  function addBCC($address){ $this->bcc[] = $address; }
  function addReplyTo($email){ $this->addHeader("Reply-to: $email"); }

  function setContent($text){ $this->content = $text; }
  function setSubject($text){ $this->subject = $text; }
  function setFrom($text){ $this->addHeader("From: $text"); }
  function setHTML(){ $this->contentType = "text/html"; }
  function setPlain(){ $this->contentType = "text/plain"; }
  function setPriority($priority){ $this->priority = $priority; }

  function addReadConfirmationEmail($email){ $this->addHeader("Disposition-Notification-To: <$email>"); }
  
  function addHeader($text){ $this->header[] = $text; }
  function addAttachment($file, $name, $type){
    if(file_exists($file)){
      $this->attachments[] = array('path' => $file, 'name' => $name, 'type' => $type);
    }
  }

  function send(){
    $to = implode(", ", $this->to);
    if($this->cc){ $this->addHeader("Cc: ".implode(", ", $this->cc)); }
    if($this->bcc){ $this->addHeader("Bcc: ".implode(", ", $this->bcc)); }
    if(count($this->attachments)){ $this->appendAttachments(); }
    $this->addAdditionalHeaders();
    $header = implode("\r\n", $this->header);
    return mail($to, $this->subject, $this->content, $header);
  }

  function appendAttachments(){
    $boundary = "H2O-".time();
    $this->addHeader("Content-Type: multipart/alternitive; boundary=$boundary");
    if($this->content){
      $content = "–$boundary\r\n";
      $content .= "Content-Transfer-Encoding: {$this->encoding}\r\n";
      $content .= "Content-type: {$this->contentType}; charset={$this->charSet}\r\n\r\n";
      $this->content = $content.$this->content."\r\n\r\n\r\n";
    }
    foreach($this->attachments as $files){
      $data = chunk_split(base64_encode(implode("", file($files['path']))));
      $attachment = "–$boundary\r\n";
      $attachment .= "Content-Transfer-Encoding: base64\r\n";
      $attachment .= "Content-Type: {$files['type']}; name={$files['name']}\r\n";
      $attachment .= "Content-Disposition: attachment; filename={$files['name']}\r\n\r\n";
      $attachment .= "$data\r\n";
      $this->content .= $attachment;
      unset($attachment);
    }
    $this->content .= "–$boundary–";
  }

  function addAdditionalHeaders(){
    $this->addHeader("MIME-Version: 1.0");
    if(!count($this->attachments)){
      $this->addHeader("Content-type: {$this->contentType}; charset={$this->charSet}");
    }
    $this->addHeader("X-Priority: $this->priority");
  }
}

?>

