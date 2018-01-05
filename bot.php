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

			if($text=="/h"){
				$messages = [
					'type' => 'text',
					'text' => '
/h ดูคำสั่ง command

/p ดูราคาเหรียญภาพรวม

/c {symbol} ดูเหรียญรายตัว ตัวอย่าง /c eth

/cal {amount} {symbol} คำนวนราคาเหรียญ ตัวอย่าง /cal 1 eth'
				];	
				$match_count = $match_count+1;
			}
			
			if(substr($text, 0, 2) == "/c"){
				$text_index = explode(' ', $text);
				$symbol = trim($text_index[1]);
				$symbol = strtolower($symbol);
				$thb_rate = 33;
				$bx_price_res = "N/A";
				if($symbol=="btc"){ $bx_price_res = '฿'.number_format($bx_price->{1}->last_price,2).' ('.fillPlus($bx_price->{1}->change).'%)'; }
				if($symbol=="eth"){ $bx_price_res = '฿'.number_format($bx_price->{21}->last_price,2).' ('.fillPlus($bx_price->{21}->change).'%)'; }
				if($symbol=="das"){ $bx_price_res = '฿'.number_format($bx_price->{22}->last_price,2).' ('.fillPlus($bx_price->{22}->change).'%)'; }
				if($symbol=="xrp"){ $bx_price_res = '฿'.number_format($bx_price->{25}->last_price,2).' ('.fillPlus($bx_price->{25}->change).'%)'; }
				if($symbol=="omg"){ $bx_price_res = '฿'.number_format($bx_price->{26}->last_price,2).' ('.fillPlus($bx_price->{26}->change).'%)'; }
				if($symbol=="bch"){ $bx_price_res = '฿'.number_format($bx_price->{27}->last_price,2).' ('.fillPlus($bx_price->{27}->change).'%)'; }
				if($symbol=="evx"){ $bx_price_res = '฿'.number_format($bx_price->{28}->last_price,2).' ('.fillPlus($bx_price->{28}->change).'%)'; }
				if($symbol=="xzc"){ $bx_price_res = '฿'.number_format($bx_price->{29}->last_price,2).' ('.fillPlus($bx_price->{29}->change).'%)'; }
				if($symbol=="ltc"){ $bx_price_res = '฿'.number_format($bx_price->{30}->last_price,2).' ('.fillPlus($bx_price->{30}->change).'%)'; }

				$coin = callService('https://minethecoin.com/api/coins/symbol/'.$symbol);
				if(isset($coin->id) && $symbol!=""){

					$coin_price = $coin->price_usd;
					$messages = ['type' => 'text','text' => strtoupper($coin->symbol).' - '.$coin->name.'
ราคานอก : ฿'.number_format($thb_rate*$coin_price,2).' ('.fillPlus($coin->percent_change_24h).'%)
ราคา BX : '.$bx_price_res.' 
'];
					$match_count = $match_count+1;	
				}
			}

			/*if($text=="จาวิส ขอราคา ETH" || $text=="จาวิส ขอราคา eth" || $text=="จาวิส ราคา ETH" || $text=="จาวิส ราคา eth"){
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
			if($text=="จาวิสขอรูปไผ่"){
				
				$messages = [
					'type' => 'image',
					'originalContentUrl' => 'https://scontent.fbkk13-1.fna.fbcdn.net/v/t1.0-0/s600x600/13709843_1173023019434822_5044201604897757811_n.jpg?oh=26b06ff06a7896179e39f4abdbd86a31&oe=5A32C63D',
					'previewImageUrl' => 'https://scontent.fbkk13-1.fna.fbcdn.net/v/t1.0-0/s600x600/13709843_1173023019434822_5044201604897757811_n.jpg?oh=26b06ff06a7896179e39f4abdbd86a31&oe=5A32C63D',
				];	
				$match_count = $match_count+1;
			}

			if(preg_match('/จาวิส/',$text) && preg_match('/มุข/',$text)){
				$msg = array('ข้าไม่หล่อเป็นประโยคบอกเล่า แต่ข้ารักเจ้าเป็นประโยคบอกร๊ากกก','ถึงภายนอกอาจจะดูไม่เผ็ดแต่ภายในจัดว่าเด็ดบอกเลย','ว่างกว่าเงินกระเป๋า ก็ใจเรานี่แหละ','ถูกหวยยังพอมีหวัง แต่ถ้าถูกใจเธอจังจะพอมีหวังหรือเปล่า','ห็นกูเป็นทางผ่าน เดี๋ยวกูจะตั้งด่านเก็บภาษี','พี่อาจไม่น่าเคารพ แต่พี่ก็น่าคบนะน้อง','หญิงใดไม่หื่น หญิงนั้นฝืนธรรมชาติ','เลิกตีเถอะป้อม ขึ้นค่อมเราดีกว่า','ขนมปังอะเรียกฟามเฮา แต่ถ้าเป็นขนมเราอะเค้าเรียกฟามรักกก','ขับรถต้องรู้เกียร์ อยากได้เมียต้องรู้ใจ','ถ้าโดนฝนก็คงเป็นไข้ แต่ถ้าโดนใจก็คงเป็นเธอออ💋💋🙃🙃');
				$k = array_rand($msg);
				$v = $msg[$k];
				$messages = [
					'type' => 'text',
					'text' => $v
				];	
				$match_count = $match_count+1;
			}

			if(preg_match('/จาวิส/',$text) && preg_match('/กิ๊ก/',$text)){
				$msg = array('มีหลายคักหำบ่เคยแห้งหมอหนิ 555','มีหลายจนปั้นหำตากบ่ทันผุ่นแหล่ว');
				$k = array_rand($msg);
				$v = $msg[$k];
				$messages = [
					'type' => 'text',
					'text' => $v
				];	
				$match_count = $match_count+1;
			}

			if(preg_match('/จาวิส/',$text) && preg_match('/เป็นไง/',$text)){
				$msg = array('หน้าไม่คม นมไม่มี ไม่มีอะไรดี ไม่มีเหี้ยอะไรเลย','อย่าแรดให้มากนะน้อง เดี๋ยวจะท้องไม่มีพ่อ');
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
			*/

			if(preg_match('/จาวิส แรงขุด/',$text)){
				$text_index = explode(' ', $text);
				$coins = callService('https://whattomine.com/coins.json',1);
				$type = $text_index[2];
				$hashrate = $text_index[3];
				$message = "";

				if($type=="" || $hashrate==""){
					exit;
				}

				$type = strtolower($type);

				$allowed_types = ['แดง','เขียว','เขียวlbc','sigt','cn'];

				if(!in_array($type, $allowed_types)){
					exit;
				}

				$display_algorithm = "";

				foreach ($coins->coins as $key => $row) {

					if($type=="แดง" && $row->algorithm!="Ethash"){
						continue;
					}
					if($type=="เขียว" && $row->algorithm!="Equihash"){
						continue;
					}
					if($type=="เขียวlbc" && $row->algorithm!="LBRY"){
						continue;
					}
					if($type=="sigt" && $row->algorithm!="Skunkhash"){
						continue;
					}
					if($type=="cn" && $row->algorithm!="CryptoNight"){
						continue;
					}
					if($row->tag=="NICEHASH"){
						continue;
					}

					$multiply_hash = 1;
					if($row->algorithm=="Ethash" || $row->algorithm=="LBRY" || $row->algorithm=="Skunkhash"){
						$multiply_hash = 1000000;
						$display_algorithm = $row->algorithm;
					}

					$userRatio = $hashrate*$multiply_hash / $row->nethash;
					$blocksPerMin = 60.0 / $row->block_time;
					$coinPerMin = $blocksPerMin * $row->block_reward;

					$price = $row->exchange_rate*$bx_price->{1}->last_price;//btc

					$earnings_min = $userRatio * $coinPerMin;
					$earnings_hour = $earnings_min * 60;
					$earnings_day = $earnings_hour * 24;
					$earnings_week = $earnings_day * 7;
					$earnings_month = $earnings_day * 30;
					$earnings_year = $earnings_day * 365;

					$message .= $row->tag."
รายได้ต่อวัน = ".number_format($earnings_day*$price)." บาท
เหรียญต่อวัน = ".number_format($earnings_day,3)." เหรียญ

";
				}

				$unit = "";

				if($type=="แดง" || $type=="เขียวLBC"){
					$unit = " Mh/s";
					$display_algorithm = 'Ethash';
				}
				if($type=="เขียว"){
					$unit = " Sols/s";
					$display_algorithm = 'Equihash';
				}
				if($type=="เขียวLBC"){
					$display_algorithm = 'LBRY';
				}
				if($type=="cn"){
					$display_algorithm = 'CryptoNight';
				}

				$messages = [
					'type' => 'text',
					'text' => "Algorithm ".$display_algorithm." 
แรงขุด ".$hashrate."".$unit." ได้
". $message
				];	
				$match_count = $match_count+1;
			}



			if(substr($text, 0, 4)=="/cal"){
				$text_index = explode(' ', $text);
				if(isset($text_index[1]) && isset($text_index[2])){
					$amount = (float)trim($text_index[1]);
					$coin_type = trim($text_index[2]);

					if($coin_type=='eth' || $coin_type=="ETH"){
						$coin_type = 'ETH';
						$price = $bx_price->{21}->last_price;
					}
					if($coin_type=='btc' || $coin_type=="BTC"){
						$coin_type = 'BTC';
						$price = $bx_price->{1}->last_price;
					}
					if($coin_type=='bch' || $coin_type=="BCH"){
						$coin_type = 'BCH';
						$price = $bx_price->{27}->last_price;
					}
					if($coin_type=='das' || $coin_type=="DAS"){
						$coin_type = 'DAS';
						$price = $bx_price->{22}->last_price;
					}
					if($coin_type=='ltc' || $coin_type=="LTC"){
						$coin_type = 'LTC';
						$price = $bx_price->{30}->last_price;
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
					if($coin_type=='xrp' || $coin_type=="XRP"){
						$coin_type = 'XRP';
						$price = $bx_price->{25}->last_price;
					}
					if($coin_type=='omg' || $coin_type=="OMG"){
						$coin_type = 'OMG';
						$price = $bx_price->{26}->last_price;
					}
					if($coin_type=='xmr' || $coin_type=="XMR"){
						$xmr =callService('https://api.coinmarketcap.com/v1/ticker/monero/?convert=THB',1);
						$coin_type = 'XMR';
						$price = $xmr[0]->price_thb;
					}

					$messages = [
						'type' => 'text',
						'text' => $amount.' '.$coin_type.' =  '.number_format($amount * $price,2).' บาท '
					];	
					$match_count = $match_count+1;
				}
			}

			if(preg_match('/จาวิส คำนวนราคา/',$text) || preg_match('/จาวิส คำนวน/',$text)){
				$text_index = explode(' ', $text);
				if(isset($text_index[2]) && isset($text_index[3])){
					$amount = (float)trim($text_index[2]);
					$coin_type = trim($text_index[3]);

					if($coin_type=='eth' || $coin_type=="ETH"){
						$coin_type = 'ETH';
						$price = $bx_price->{21}->last_price;
					}
					if($coin_type=='btc' || $coin_type=="BTC"){
						$coin_type = 'BTC';
						$price = $bx_price->{1}->last_price;
					}
					if($coin_type=='bch' || $coin_type=="BCH"){
						$coin_type = 'BCH';
						$price = $bx_price->{27}->last_price;
					}
					if($coin_type=='das' || $coin_type=="DAS"){
						$coin_type = 'DAS';
						$price = $bx_price->{22}->last_price;
					}
					if($coin_type=='ltc' || $coin_type=="LTC"){
						$coin_type = 'LTC';
						$price = $bx_price->{30}->last_price;
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
					if($coin_type=='xrp' || $coin_type=="XRP"){
						$coin_type = 'XRP';
						$price = $bx_price->{25}->last_price;
					}
					if($coin_type=='omg' || $coin_type=="OMG"){
						$coin_type = 'OMG';
						$price = $bx_price->{26}->last_price;
					}
					if($coin_type=='xmr' || $coin_type=="XMR"){
						$xmr =callService('https://api.coinmarketcap.com/v1/ticker/monero/?convert=THB',1);
						$coin_type = 'XMR';
						$price = $xmr[0]->price_thb;
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

			if($text=="จาวิส อัพเดทราคา" || preg_match('/อัพเดทราคา/',$text) || $text=="/p"){
				$etc =callService('https://api.coinmarketcap.com/v1/ticker/ethereum-classic/?convert=THB',1);
				$zec =callService('https://api.coinmarketcap.com/v1/ticker/zcash/?convert=THB',1);
				$ltc =callService('https://api.coinmarketcap.com/v1/ticker/litecoin/?convert=THB',1);
				//$sigt =callService('https://www.cryptopia.co.nz/api/GetMarket/SIGT_BTC',1);
				$xmr =callService('https://api.coinmarketcap.com/v1/ticker/monero/?convert=THB',1);
				$knc =callService('https://api.coinmarketcap.com/v1/ticker/kyber-network/?convert=THB',1);
				$btg =callService('https://api.coinmarketcap.com/v1/ticker/bitcoin-gold/?convert=THB',1);
				$etn =callService('https://api.coinmarketcap.com/v1/ticker/electroneum/?convert=THB',1);
				$neo =callService('https://api.coinmarketcap.com/v1/ticker/neo/?convert=THB',1);
				$iota =callService('https://api.coinmarketcap.com/v1/ticker/iota/?convert=THB',1);
				$tierion =callService('https://api.coinmarketcap.com/v1/ticker/tierion/?convert=THB',1);
				$icon =callService('https://api.coinmarketcap.com/v1/ticker/icon/?convert=THB',1);
				$messages = [
			'type' => 'text',
			'text' => 'BTC - ฿'.number_format($bx_price->{1}->last_price,0).' ('.fillPlus($bx_price->{1}->change).'%)
BCH - ฿'.number_format($bx_price->{27}->last_price,0).' ('.fillPlus($bx_price->{27}->change).'%)
ETH - ฿'.number_format($bx_price->{21}->last_price,0).' ('.fillPlus($bx_price->{21}->change).'%)
ETC - ฿'.number_format($etc[0]->price_thb,0).' ('.fillPlus($etc[0]->percent_change_24h).'%)
ZEC - ฿'.number_format($zec[0]->price_thb,0).' ('.fillPlus($zec[0]->percent_change_24h).'%)
XRP - ฿'.number_format($bx_price->{25}->last_price,2).' ('.fillPlus($bx_price->{25}->change).'%)
DAS - ฿'.number_format($bx_price->{22}->last_price,0).' ('.fillPlus($bx_price->{22}->change).'%)
LTC - ฿'.number_format($bx_price->{30}->last_price,0).' ('.fillPlus($bx_price->{30}->change).'%)
XMR - ฿'.number_format($xmr[0]->price_thb,0).' ('.fillPlus($xmr[0]->percent_change_24h).'%)
OMG - ฿'.number_format($bx_price->{26}->last_price,0).' ('.fillPlus($bx_price->{26}->change).'%)
XZC - ฿'.number_format($bx_price->{29}->last_price,0).' ('.fillPlus($bx_price->{29}->change).'%)
KNC - ฿'.number_format($knc[0]->price_thb,2).' ('.(float)fillPlus($knc[0]->percent_change_24h).'%)
BTG - ฿'.number_format($btg[0]->price_thb,0).' ('.(float)fillPlus($btg[0]->percent_change_24h).'%)
ETN - ฿'.number_format($etn[0]->price_thb,2).' ('.(float)fillPlus($etn[0]->percent_change_24h).'%)
NEO - ฿'.number_format($neo[0]->price_thb,2).' ('.(float)fillPlus($neo[0]->percent_change_24h).'%)
IOTA - ฿'.number_format($iota[0]->price_thb,2).' ('.(float)fillPlus($iota[0]->percent_change_24h).'%)
TNT - ฿'.number_format($tierion[0]->price_thb,2).' ('.(float)fillPlus($tierion[0]->percent_change_24h).'%)
ICX - ฿'.number_format($icon[0]->price_thb,2).' ('.(float)fillPlus($icon[0]->percent_change_24h).'%)
'
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
			if($text=="อัพเดทข่าว" || $text=="/n"){
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