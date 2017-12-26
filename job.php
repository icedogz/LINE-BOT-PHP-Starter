<?php
include "lib.php";

$bx_price_url = "https://bx.in.th/api/";
$bx_price = callService($bx_price_url,1);
$etc =callService('https://api.coinmarketcap.com/v1/ticker/ethereum-classic/?convert=THB',1);
$zec =callService('https://api.coinmarketcap.com/v1/ticker/zcash/?convert=THB',1);

$messages = [
			'type' => 'text',
			'text' => 'โย่ๆๆ โย่ๆๆ'
		];	

pushMessage($messages);		