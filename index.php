<?php
header('Content-type: text/html; charset=UTF-8');
session_start();
require_once ('core/init.php');
require_once ('config.php');
require_once ('core/utils/function.php');

$core = new Core();
$core->run();
$cont = XML::transform(null, $core->xsl);
// var_dump($core->xsl);
echo $cont;
exit();