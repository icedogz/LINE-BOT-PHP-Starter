<?php
include "lib.php";

$feed =callService('https://siamblockchain.com/feed/',1,'xml');
var_dump($feed);exit;

header('Content-Type:application/json');
echo json_encode($feed);

