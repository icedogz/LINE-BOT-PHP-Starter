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
			$bx_price_url = "https://bx.in.th/api/";
			$bx_price = callService($bx_price_url,1);
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
			if($text=="จาวิส ขอราคา ETH" || $text=="จาวิส ขอราคา eth" || $text=="จาวิส ราคา ETH" || $text=="จาวิส ราคา eth"){
				$messages = [
					'type' => 'text',
					'text' => 'ETH ราคา '.number_format($bx_price->{21}->last_price,2).' บาท เด้อลูกพี่'
				];	
				$match_count = $match_count+1;
			}
			if($text=="จาวิส ขอราคา BTC" || $text=="จาวิส ขอราคา btc" || $text=="จาวิส ราคา BTC" || $text=="จาวิส ราคา btc"){
				$messages = [
					'type' => 'text',
					'text' => 'BTC ราคา '.number_format($bx_price->{1}->last_price,2).' บาท เด้อลูกพี่'
				];	
				$match_count = $match_count+1;
			}
			if($text=="จาวิส ขอราคา ETC" || $text=="จาวิส ขอราคา etc" || $text=="จาวิส ราคา ETC" || $text=="จาวิส ราคา etc"){
				$etc =callService('https://api.coinmarketcap.com/v1/ticker/ethereum-classic/?convert=THB',1);
				$messages = [
					'type' => 'text',
					'text' => 'ETC ราคา '.number_format($etc->price_thb,2).' บาท เด้อลูกพี่'
				];	
				$match_count = $match_count+1;
			}

			if(preg_match('/จาวิส คำนวนราคา/',$text) || preg_match('/จาวิส คำนวน/',$text)){
				$text_index = explode(' ', $text);
				if(isset($text_index[2]) && isset($text_index[3])){
					$amount = (float)$text_index[2];
					$coin_type = $text_index[3];

					if($coin_type=='eth' || $coin_type=="ETH"){
						$coin_type = 'ETH';
						$price = $bx_price->{21}->last_price;
					}
					if($coin_type=='btc' || $coin_type=="BTC"){
						$coin_type = 'BTC';
						$price = $bx_price->{1}->last_price;
					}
					if($coin_type=='etc' || $coin_type=="ETC"){
						$etc =callService('https://api.coinmarketcap.com/v1/ticker/ethereum-classic/?convert=THB',1);
						$coin_type = 'ETC';
						$price = $etc[0]->price_thb;
					}

					$messages = [
						'type' => 'text',
						'text' => $amount.' '.$coin_type.' =  '.number_format($amount * $price,2).' บาท เด้อลูกพี่'
					];	
					$match_count = $match_count+1;
				}
			}

			if(preg_match('/จาวิส top coin/',$text)){
				$topcoins=callService('https://api.coinmarketcap.com/v1/ticker/?limit=10',1);

				$txt = "";
				$i=1;
				foreach($topcoins as $index => $row){
					$txt .= $i.'. '.$row->name." (".$row->symbol.") \nPrice: $".number_format($row->price_usd,2)." USD\n\n";
					$i++;
				}
				
				$messages = [
					'type' => 'text',
					'text' => $txt
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