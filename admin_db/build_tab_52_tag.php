<?php
require_once 'DB_Handler.php';

$sql = "CREATE TABLE tag
(
tag_id int NOT NULL AUTO_INCREMENT, 
CONSTRAINT tag_id PRIMARY KEY(tag_id),
tag_name varchar(255)
)";

new DB_Handler($sql);
?>