<?php
include "lib.php";

$feed =callService('https://siamblockchain.com/feed/',1,'xml');


header('Content-Type:application/json');
echo json_encode($feed);

