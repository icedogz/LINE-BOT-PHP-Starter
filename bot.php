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

$wallet = str_replace('0x', '', $_GET['miner']);
$bx_price_url = "https://bx.in.th/api/";
$bx_price = callService($bx_price_url,1);





// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
// Validate parsed JSON data
if (!is_null($events['events'])) {
	// Loop through each event
	foreach ($events['events'] as $event) {
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
			// Get text sent
			$text = $event['message']['text'];
			// Get replyToken
			$replyToken = $event['replyToken'];

			// Build message to reply back
			$messages = [
				'type' => 'text',
				'text' => $text
			];

			$match_count = 0;
			if($text=="จาวิส ขอราคา ETH" || $text=="จาวิส ขอราคา eth"){
				$messages = [
					'type' => 'text',
					'text' => 'ETH ราคา '.number_format($bx_price->{21}->last_price,2).' บาท เด้อลูกพี่'
				];	
				$match_count = $match_count+1;
			}
			if($text=="จาวิส ขอราคา BTC" || $text=="จาวิส ขอราคา btc"){
				$messages = [
					'type' => 'text',
					'text' => 'BTC ราคา '.number_format($bx_price->{1}->last_price,2).' บาท เด้อลูกพี่'
				];	
				$match_count = $match_count+1;
			}

			if($text=="จาวิส"){
				$msg = array('เรียกหาซิแตกบ่คับลูกพี่','ครับลูกพี่','ฮ้วยเรียกเฮ็ดหยัง');
				$k = array_rand($msg);
				$v = $msg[$k];
				$messages = [
					'type' => 'text',
					'text' => $v
				];	
				$match_count = $match_count+1;
			}
			if($text=="โตโต้"){
				$msg = array('กากมากคนนี้','กากกว่านี้ไม่มีแล้ว','กากบักคักหมอนี่');
				$k = array_rand($msg);
				$v = $msg[$k];
				$messages = [
					'type' => 'text',
					'text' => $v
				];	
				$match_count = $match_count+1;
			}

			if (preg_match('/จาวิส/',$text) && $match_count==0){
				$messages = [
					'type' => 'text',
					'text' => 'จักนำเด้อคับลูกพี่ จักเว่าพิสังดอก'
				];	
				$match_count = $match_count+1;
			}

			if($match_count>0){
				replyMessage($replyToken,$messages);
			}
		}
	}
}
echo "OK";