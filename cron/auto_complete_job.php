<?php

$base = dirname(dirname(__FILE__)); // now $base contains "app"
include($base . '/includes/connection.php');
include($base . '/includes/function.php');
/*    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);*/

echo $abcd = allotLeads(6, $con, "`country`.`id`='101'", 0,0);

echo "---";

echo "---".date("Y-m-d H:i:s")."---auto-allot-lead--\n\n";

?>