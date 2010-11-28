<?php
require_once '../include/autoload.php';

$sql = "CREATE TABLE user
(
user_id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT user_id PRIMARY KEY(user_id),
password varchar(255) NOT NULL,
handle varchar(255),
UNIQUE (handle),
email varchar(255) NOT NULL,
UNIQUE (email),
timezone varchar(255) NOT NULL,
date_format varchar(255) NOT NULL,
active int(1) NOT NULL
)";

new db_handler($sql);

?>