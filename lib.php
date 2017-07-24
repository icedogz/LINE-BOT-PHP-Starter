<?php 
function callService($url,$cache=0,$format = 'json'){
	

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

	if($format=='json'){
    	return  json_decode($output);    
	}else if($format == 'xml'){
		$xml = simplexml_load_string($output);
		$json = json_encode($xml);
		return json_decode($json);
	}else{
		return false;
	}
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


 ?>