<?php
class SiteInfo {
  private $ini_file;
  private $ini_arr;

  public function __construct($parse_now = true) {
    $this->setINIFile("site.ini");
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

  public function __toString() {
    foreach ($this->ini_arr as $key => $value) {
      $ini_str .= "ini_arr[".$key."]=".$value."\n";
    }
    return $ini_str;
  }
}