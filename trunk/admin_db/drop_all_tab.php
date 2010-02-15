<?php
//*
require_once '../include/autoloader.php';
$directory = '.';
$handler = opendir($directory);
while ($file = readdir($handler)) {
  if (preg_match("/drop_tab_/",$file))
  {
    echo "<h3>Require: $file </h3>\n";
    require_once $file;
    $db_obj->__destruct();
  }
}
closedir($handler);
/*
DROP TABLE  `tag_assoc` ,
`txn_history` ,
`user_assoc` ;

DROP TABLE  `tag` ,
`txn` ,
`txn_type` ,
`user` ;

DROP TABLE  `acct` ,
`acct_type` ,
`inst` ;

//*/
/*
require_once '../include/autoloader.php';

try {
  $db_obj = new DBCon();
  $sql = "DROP TABLE  `tag_assoc` ,
`txn_history` ,
`user_assoc` ;";
  $db_obj->query($sql);
  echo "COMPLETED:<BR>\n".$sql."<BR>\n";
  } catch (Exception $err) {
  echo 'Caught exception: ',  $err->getMessage(), "\n";
}
try {
  $sql = "DROP TABLE  `tag` ,
`txn` ,
`txn_type` ,
`user` ;";
  $db_obj->query($sql);
  echo "COMPLETED:<BR>\n".$sql."<BR>\n";
  } catch (Exception $err) {
  echo 'Caught exception: ',  $err->getMessage(), "\n";
}
try {
  $sql = "DROP TABLE  `acct` ,
`acct_type` ,
`inst` ;";
  $db_obj->query($sql);
  echo "COMPLETED:<BR>\n".$sql."<BR>\n";
} catch (Exception $err) {
  echo 'Caught exception: ',  $err->getMessage(), "\n";
}
//*/
?>