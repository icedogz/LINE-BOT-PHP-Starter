<?php
include "lib.php";
$bx_price_url = "https://bx.in.th/api/";
$bx_price = callService($bx_price_url,0);
var_dump($bx_price);