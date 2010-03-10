<?php
class DBCon {
  private $database;
  private $link;
  private $result;
  private $ini_file = "vars/db.ini";
  private $ini_arr = array();
  public $sql;

  function __construct($connect_bool=true) {
    if ($connect_bool)
    {
      try {
        $this->connect();
        $this->selectDB();
      } catch (Exception $err) {
        throw $err;
      }
      return $this->link;
    }else{
      return true;
    }
  }

  public function connect() {
    if (! $ini_arr = parse_ini_file($this->ini_file)) {
      throw new Exception("ERROR:DBCon:connect - parse_ini_file - Failed to parse '{$this->ini_file}' <br />\n");
    } elseif (! $this->link = mysql_connect($ini_arr['dbinfo.host'],$ini_arr['dbinfo.user'],$ini_arr['dbinfo.pw'])) {
      throw new Exception("ERROR:DBCon:connect - mysql_connect - " . mysql_error() . " <br />\n");
    } else {
      return true;
    }
  }

  public function selectDB() {
    if (! $ini_arr = parse_ini_file($this->ini_file)) {
      throw new Exception("ERROR:DBCon:selectDB - parse_ini_file - Failed to parse '{$this->ini_file}' <br />\n");
      //} elseif (! $this->setDB($ini_arr['dbinfo.db'])) {
      //      throw new Exception("ERROR:DBCon:selectDB - setDB - Failed to set database '".$ini_arr['dbinfo.db']."' <br />\n");
    } elseif (! mysql_select_db($ini_arr['dbinfo.db'],$this->link)) {
      throw new Exception("ERROR:DBCon:selectDB - mysql_select_db - " . mysql_error() . " <br />\n");
    } else {
      return true;
    }
  }

  public function query($sql) {
    if (!empty($sql)){
      $this->sql = $sql;
      $this->result = mysql_query($sql,$this->link);
      if (! $this->result) {
        throw new Exception("ERROR:DBCon:query - mysql_query - " . mysql_error() . " trying to execute " . $sql . " <br />\n");
      } else {
        return $this->result;
      }
    } else {
      return false;
    }
  }

  public function num($result="") {
    if (empty($result)){ $result = $this->result; }
    return mysql_num_rows($result);
  }

  public function lastId() {
    return mysql_insert_id($this->link);
  }

  public function fetch($result="") {
    if (empty($result)){ $result = $this->result; }
    return mysql_fetch_array($result);
  }

  public function setDB($database="") {
    $this->database = $database;
  }

  public function setINIFile($ini_file="") {
    $this->ini_file = $ini_file;
  }

  public function getDB() {
    return $this->database;
  }

  public function getINIFile() {
    return $this->ini_file;
  }


  function __destruct(){
    mysql_close($this->link);
  }
}
?>