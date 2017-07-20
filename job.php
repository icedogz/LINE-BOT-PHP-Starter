<?php
include "lib.php";

$bx_price_url = "https://bx.in.th/api/";
$bx_price = callService($bx_price_url,1);
$etc =callService('https://api.coinmarketcap.com/v1/ticker/ethereum-classic/?convert=THB',1);
$zec =callService('https://api.coinmarketcap.com/v1/ticker/zcash/?convert=THB',1);

$messages = [
			'type' => 'text',
			'text' => 'อัพเดทราคา
ETH : '.number_format($bx_price->{21}->last_price,2).' บาท
BTC : '.number_format($bx_price->{1}->last_price,2).' บาท
ETC : '.number_format($etc[0]->price_thb,2).' บาท
ZEC : '.number_format($zec[0]->price_thb,2).' บาท'
		];	

pushMessage($messages);		