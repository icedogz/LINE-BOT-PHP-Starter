<?php
include "lib.php";

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
			if($text=="Group ID"){
				$messages = [
					'type' => 'text',
					'text' => $event['source']['groupId']
				];	
				$match_count = $match_count+1;
			}
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
			if($text=="จาวิส ขอราคา ZEC" || $text=="จาวิส ขอราคา zec" || $text=="จาวิส ราคา ZEC" || $text=="จาวิส ราคา zec"){
				$zec =callService('https://api.coinmarketcap.com/v1/ticker/zcash/?convert=THB',1);
				$messages = [
					'type' => 'text',
					'text' => 'ZEC ราคา '.number_format($zec[0]->price_thb,2).' บาท เด้อลูกพี่'
				];	
				$match_count = $match_count+1;
			}
			if($text=="จาวิส ขอราคา ETC" || $text=="จาวิส ขอราคา etc" || $text=="จาวิส ราคา ETC" || $text=="จาวิส ราคา etc"){
				$etc =callService('https://api.coinmarketcap.com/v1/ticker/ethereum-classic/?convert=THB',1);
				$messages = [
					'type' => 'text',
					'text' => 'ETC ราคา '.number_format($etc[0]->price_thb,2).' บาท เด้อลูกพี่'
				];	
				$match_count = $match_count+1;
			}
			if($text=="ไผ่"){
				$msg = array('คนนี้หล่อขั้นเทพ','กรุณาเรียกว่าคุณไผ่นะครับ','หล่อสัสๆ หล่อเหี้ยๆ หล่อกว่านี้ก็โดม ปกร ลัม ละฮะ');
				$k = array_rand($msg);
				$v = $msg[$k];
				$messages = [
					'type' => 'text',
					'text' => $v
				];	
				$match_count = $match_count+1;
			}

			if(preg_match('/จาวิส/',$text) && preg_match('/หล่อไหม/',$text)){
				$msg = array('โอ้ยหน้ายังกะฮวกกบแหม','โอยหน้าคือจังคนปวดขี้นิแมะ','เทียบผมบ่ติดหรอกครับลูกพี่','ขี้ร้ายๆอย่ามาถาม','เฮ็ดหน้าคือขี้คาดากหนิ');
				$k = array_rand($msg);
				$v = $msg[$k];
				$messages = [
					'type' => 'text',
					'text' => $v
				];	
				$match_count = $match_count+1;
			}
			if(preg_match('/จาวิส/',$text) && preg_match('/สวยไหม/',$text)){
				$msg = array('โอ้ยหน้ายังกะฮวกกบแหม','โอยหน้าคือจังคนปวดขี้นิแมะ','ขี้ร้ายๆอย่ามาถาม','เฮ็ดหน้าคือขี้คาดากหนิ');
				$k = array_rand($msg);
				$v = $msg[$k];
				$messages = [
					'type' => 'text',
					'text' => $v
				];	
				$match_count = $match_count+1;
			}


			if(preg_match('/จาวิส/',$text) && preg_match('/ขุดไรดี/',$text)){
				
				$messages = [
					'type' => 'text',
					'text' => 'เข้า http://whattomine.com แล้วใส่แรงขุดตัวเองนะ ไม่ต้องมาถามผม'
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
					if($coin_type=='zec' || $coin_type=="ZEC"){
						$zec =callService('https://api.coinmarketcap.com/v1/ticker/zcash/?convert=THB',1);
						$coin_type = 'ZEC';
						$price = $zec[0]->price_thb;
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

			if($text=="จาวิส อัพเดทราคา" || preg_match('/อัพเดทราคา/',$text)){
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
			if($text=="อัพเดทข่าว"){
				$feed =callService('https://siamblockchain.com/feed/',1,'xml');
				$text = '';
				$i=1;
				foreach($feed->channel->item as $row){

					if($i>5){
						break;
					}
					$short_url = postJson('https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyDojB8lNm0KJgBVD_tE_H4BTM3AbhvTFnQ',[
						'longUrl'=>$row->link
					]);
					$text .= $row->title.'
'.date('d/m/y',strtotime($row->pubDate)).'
'.$short_url->id.'

';
					$i++;
				}

				$messages = [
					'type' => 'text',
					'text' => $text
				];	
				$match_count = $match_count+1;
			}
			if(preg_match('/นอน/',$text)){
				$msg = array('หลับฝันดีเด้อคับ','ฝันดีครับลูกพี่','หลับฝันดีตีกะหรี่ทั้งคืนครับ');
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
			if($text=="ใครสร้างจาวิส"){
				$msg = array('ลูกพี่อาทไงคับลูกพี่ผมเองเทพคักคนนี้ ใครอยากใส่โปรแกรมไรให้ผม ลูกพี่บอกขอ 3 ขวดพอ 555');
				$k = array_rand($msg);
				$v = $msg[$k];
				$messages = [
					'type' => 'text',
					'text' => $v
				];	
				$match_count = $match_count+1;
			}
			if($text=="เดฟ"){
				$msg = array('เป็นคนรักเดียวใจเดียว','เป็นคนมั่นคงต่อความรู้สึก','เป็นคนไม่เจ้าชู้','เป็นคนรักจริง แต่ตังไม่มี');
				$k = array_rand($msg);
				$v = $msg[$k];
				$messages = [
					'type' => 'text',
					'text' => $v
				];	
				$match_count = $match_count+1;
			}

			if (preg_match('/จาวิส/',$text) && $match_count==0){
				$msg = array('จักนำเด้อคับลูกพี่','จักเว่าพิสังดอก','ช่วยพูดให้เข้าใจหน่อย','โอ้ย ถามพิสังน้อ');
				$k = array_rand($msg);
				$v = $msg[$k];
				$messages = [
					'type' => 'text',
					'text' => $v
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