<?php
function callService($url,$cache=0){
	

    $cache_file = 'cache/'.md5($url);

    if (file_exists($cache_file) && $cache>0 && (filemtime($cache_file) > (time() - 60 * $cache ))) {
	 
	   $output = file_get_contents($cache_file);
	} else {
	  
	   $ch = curl_init();
	   curl_setopt($ch, CURLOPT_URL, $url);
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
	   $output = curl_exec($ch);
	   curl_close($ch);  
	   file_put_contents($cache_file, $output, LOCK_EX);
	}


    return  json_decode($output);    
}

function replyMessage($replyToken,$messages){
	$access_token = 'KqbT+kBzOYBY7UJoOqa/ZXcaldF2/wwBkY++L4XdDatpJj+FcT2X5z8GJGEDhKij9I5taNONaFM9TvG+MLAvcyf1/TB+tkkFRhiEtUKw2nxf1t2D/pU8VWRvBppfOvt213geEIRcmqqOc4MSIVbT2QdB04t89/1O/w1cDnyilFU=';
	// Make a POST Request to Messaging API to reply to sender
	$url = 'https://api.line.me/v2/bot/message/reply';
	$data = [
		'replyToken' => $replyToken,
		'messages' => [$messages],
	];
	$post = json_encode($data);
	$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$result = curl_exec($ch);
	curl_close($ch);

	echo $result . "\r\n";
}

function pushMessage($messages){
	$access_token = 'KqbT+kBzOYBY7UJoOqa/ZXcaldF2/wwBkY++L4XdDatpJj+FcT2X5z8GJGEDhKij9I5taNONaFM9TvG+MLAvcyf1/TB+tkkFRhiEtUKw2nxf1t2D/pU8VWRvBppfOvt213geEIRcmqqOc4MSIVbT2QdB04t89/1O/w1cDnyilFU=';
	// Make a POST Request to Messaging API to reply to sender
	$url = 'https://api.line.me/v2/bot/message/push';
	$data = [
		'to' => 'C87bf6606099f4f85313e705c2f6829dc',
		'messages' => [$messages],
	];
	$post = json_encode($data);
	$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$result = curl_exec($ch);
	curl_close($ch);

	echo $result . "\r\n";
}


$bx_price_url = "https://bx.in.th/api/";
$bx_price = callService($bx_price_url,1);
$etc =callService('https://api.coinmarketcap.com/v1/ticker/ethereum-classic/?convert=THB',1);

$messages = [
			'type' => 'text',
			'text' => 'อัพเดทราคา
ETH : '.number_format($bx_price->{21}->last_price,2).'บาท
BTC : '.number_format($bx_price->{1}->last_price,2).'บาท
ETC : '.number_format($etc[0]->price_thb,2).' บาท'
		];	

pushMessage($messages);		