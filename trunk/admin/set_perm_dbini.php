<?php

$file = '../include/db.ini';
$mode = 0666;

$cur_mode_o = substr(sprintf('%o', fileperms($file)), -4);

printf("Editing file %s <br>\n", $file);
printf("Mode is currently: %s octal <br>\n", $cur_mode_o);
chmod($file, $mode);
printf("Mode set to: %o octal <br>\n", $mode);

$new_mode_o = substr(sprintf('%o', fileperms($file)), -4);
printf("Reading mode from file: %s octal <br>\n", $new_mode_o);

?>