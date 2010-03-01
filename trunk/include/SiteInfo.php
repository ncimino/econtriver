<?php
class SiteInfo {
  private $ini_file = "vars/site.ini";
  private $ini_arr;

  public function __construct($parse_now = true) {
    if ($parse_now) {
      $this->parseINIFile();
    }
  }

  public function setINIFile($file) {
    $this->ini_file = $file;
  }

  public function parseINIFile() {
    if (! $ini_arr = parse_ini_file($this->ini_file)) {
      throw new Exception("ERROR:SiteInfo:parseINIFile - parse_ini_file - Failed to parse '{$this->ini_file}' <br />\n");
    } else {
      foreach ($ini_arr as $key => $value) {
        $this->setINIValue($key,$value);
      }
      return true;
    }
  }

  public function setINIValue($key,$value) {
    $this->ini_arr[$key] = $value;
  }

  public function getINIValue($key) {
    return $this->ini_arr[$key];
  }

  function getSalt() { return $this->getINIValue('siteinfo.salt'); }
  function getName() { return $this->getINIValue('siteinfo.sitename'); }
  function getSubName() { return $this->getINIValue('siteinfo.subsitename'); }
  function getFromEmail() { return $this->getINIValue('siteinfo.fromemail'); }
  function getKeywords() { return $this->getINIValue('siteinfo.keywords'); }
  function getDescription() { return $this->getINIValue('siteinfo.description'); }
  function getImageDir() { return $this->getINIValue('siteinfo.imagedir'); }

  function getIconFile() { return $this->getINIValue('siteinfo.iconfile'); }
  function getIcon() { return $this->getSiteHTTP().$this->getImageDir().$this->getIconFile(); }
  function getIconType() { return $this->getINIValue('siteinfo.icontype'); }

  function getLogoFile() { return $this->getINIValue('siteinfo.logofile'); }
  function getLogoHeight() { return $this->getINIValue('siteinfo.logoheight'); }
  function getLogoWidth() { return $this->getINIValue('siteinfo.logowidth'); }
  function getLogo() { return $this->getSiteHTTP().$this->getImageDir().$this->getLogoFile(); }

  function getCssDir() { return $this->getINIValue('siteinfo.cssdir'); }
  function getCssFile() { return $this->getINIValue('siteinfo.cssfile'); }
  function getCss() { return $this->getSiteHTTP().$this->getCssDir().$this->getCssFile(); }

  function getJsDir() { return $this->getINIValue('siteinfo.jsdir'); }
  function getJsFile() { return $this->getINIValue('siteinfo.jsfile'); }
  function getJs() { return $this->getSiteHTTP().$this->getJsDir().$this->getJsFile(); }

  function getDomain() { return $_SERVER['HTTP_HOST']; }
  function getSiteHTTP() { return "http://".$this->getDomain().$this->getServerDir(); }
  function getServerRootDir() { return $_SERVER['DOCUMENT_ROOT']; }
  function getServerDir() { return $this->getINIValue('siteinfo.rootdir'); }
  function getSelf() { return $_SERVER['PHP_SELF']; }

  public function __toString() {
    foreach ($this->ini_arr as $key => $value) {
      $ini_str .= "ini_arr[".$key."]=".$value."\n";
    }
    return $ini_str;
  }
}