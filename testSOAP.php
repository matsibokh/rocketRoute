<?php
	
	//***** THIS PART MUST BE FOR GET TOKEN*****\\
	$pass="*******";
	$appiPass="**********";	
	$md5Pass=md5($pass);
	$md5API=md5($appiPass);
	
	$url="fly.rocketroute.com";
	$data='<?xml version="1.0" encoding="UTF-8" ?>
		<AUTH>
		<USR>pmatsibokh@gmail.com</USR>
		<PASSWD>'.$md5Pass.'</PASSWD>
		<DEVICEID>1299f2aa8935b9ffabcd4a2cbcd16b8d45691629</DEVICEID>
		<PCATEGORY>RocketRoute</PCATEGORY>
		<APPMD5>'.$md5API.'</APPMD5>
		</AUTH>';
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	$output = curl_exec($ch);
	curl_close($ch);
	//print_r($output);





	//***** REQUEST FOR NITAM*****\\
	$ICAO=$_REQUEST['ICAO'];

	$req='<?xml version="1.0" encoding="UTF-8"?>
		<REQWX>
		  <USR>pmatsibokh@gmail.com</USR>
		  <PASSWD>'.$md5Pass.'</PASSWD>
		  <ICAO>'.$ICAO.'</ICAO>
		</REQWX>';

	$client = new SoapClient("https://apidev.rocketroute.com/notam/v1/service.wsdl");
	$response = $client->getNotam($req);
	
	$p = xml_parser_create();
	xml_parse_into_struct($p, $response, $vals, $index);
	xml_parser_free($p);
	$notams=array();
	foreach ($index as $key => $value) {
		if($key=="NOTAM"){
			foreach ($value as $keyN => $valueN) {
				if($vals[$valueN]["type"]=="open"){
					$notams[$vals[$valueN]['attributes']["ID"]]=array();
					for($i=$valueN+1;$i<count($vals);$i++){
						if($vals[$i]["type"]!="complete"){
							break;
						}else{
							if(isset($vals[$i]["value"])){
								$notams[$vals[$valueN]['attributes']["ID"]][$vals[$i]["tag"]]=$vals[$i]["value"];
							}else{
								$notams[$vals[$valueN]['attributes']["ID"]][$vals[$i]["tag"]]=0;
							}
						}
					}
				}
			}
		}
	}
	echo(json_encode($notams));
?>
