<?php
class Site {
  public $DB;
  public $siteInfo;
  public $infoMsg;
  public $user;
  public $content;
  public $head;
  public $body;
  public $document;

  function __construct($title) {
  	$this->DB = new DBCon();
    $this->siteInfo = new SiteInfo();
    $this->infoMsg = new InfoMsg();
    $this->user = new User($this->DB,$this->siteInfo,$this->infoMsg);
    $this->document = HTML_Document::create();
    $this->head = new Head($this->document,$this->siteInfo);
    $this->body = new Body($this->document,$this->infoMsg,$this->siteInfo,$this->user,$title);
    $this->content = $this->body->divMid;
    if (preg_match("/Mozilla\/[1-4]{1}/",$this->user->getUserAgent())) {
    	$this->infoMsg->addMessage(1,'You are not using an HTML 5.0 browser, which this site requires. Try upgrading to ','Google Chrome','http://www.google.com/chrome');
    }
  }

  function replaceTitle($title) {
    $this->body->title->HTML_Element->nodeValue = $title;
  }

  function landingPage() {
    $this->replaceTitle('Free Multi-User Account and Investment Management');
    new HTML_Heading($this->content,4,'Welcome to '.$this->siteInfo->getName().'!');
    $content[] = "This site was created to help manage investment and account transactions.
    These account tracking pages allow you to share accounts and grant privileges to other 
    users so that they can add, remove, and change transactions.";

    $content[] = "There are several ideas that I have for web applications, and as time goes
    on I will continue to build more. For now I am working on a project which will
    act like a checkbook registry.  Many people are scared to put this kind of 
    information on to the internet, and I can't blame them.";

    $content[] = "Anything can be hacked (at least until we have quantum computing), so why
    even chance it.  My idea is that I wouldn't ever ask for enough information on
    this site to identify anyone.  An email account, which I will use to send an
    activation code and in the case that a user forgot their password, they could reset it
    with their email. I would store a password so that the user can log into their 
    account, and that's it.  I don't want to know more information than that.
    The more information I have on a person makes me liable, and the more incentive 
    attackers have.";

    $content[] = "If there isn't any information to steal, then how can your information be stolen?";

    $content[] = "My idea is fairly simple and is something that I started doing myself a little while ago.
    I would take an excell sheet and keep track of all my transactions in it, 
    similar to that of checkbook registry.  With one major advantage, I had an extra column
    titled, 'Bank says'.";

    $content[] = "The hardest thing for me, when I used to keep a paper registry was to find
    all of the differences between what my registry says and what the bank statement said.
    So the 'Bank says' column only keeps track of the transactions which the bank shows.
    This makes it very easy to balance, just look at the bank statement and look at 'Bank says'.";

    $content[] = "This page will be free to use. I hope I have enough time to devote to it
    to really add some great features.  Some other great aspects of doing the account registry
    online is that we can easily keep multiple accounts all in the same place which keeps everything
    very organized, we can see net totals between accounts, and we can categorize to see
    where we are spending too much money and where we need to be investing more.";

    $content[] = "Please check back often, I will be working on getting the checkbook registry up for the next few weeks.";
    
    foreach($content as $text) { 
      new HTML_Paragraph($this->content,$text);
      new HTML_Br($this->content);     
    }
  }

  function printPage() {
    $this->infoMsg->commitMessages();
    printf( '%s', $this->document->saveXML() );
  }
}
?>