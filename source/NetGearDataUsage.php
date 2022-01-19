<?php
#Written by KramWell.com - 14/MAR/2021
#This script will connect to your netgear device (tested on MR2100) and display the amount of roaming data used.

#have to call the url twice as first try gives a 302
$netgearArray = array(json_decode(getUrlContent(getUrlContent("http://192.168.1.1/sess_cd_tmp?op=%2Fapi%2Fmodel%2Ejson")),true));

#see all values
#var_dump($netgearArray);

#select the roaming data as this is all we need to display
$dataTransferredRoaming = $netgearArray[0]['wwan']['dataUsage']['generic']['dataTransferredRoaming'];

#show megabytes
echo formatBytes($dataTransferredRoaming, 1) . "<br>";

#show gigabytes
echo formatBytes($dataTransferredRoaming, 2, "GB");

###########

function getUrlContent($url){
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	$data = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
 
	if ($httpcode == "302"){
		#get new link in doc and try again.	 
		$dom = new DOMDocument;
		$dom->loadHTML($data);
		$nodes = $dom->getElementsByTagName('a');
		foreach ($nodes as $node){
				return($node->getAttribute('href'));
		}	 	 
	}
	return $data;
}

function formatBytes($bytes, $precision = 2, $unit = "MB") { 	
	$units = array('B', 'KB', 'MB'); 
	if ($unit == "GB"){
		$units = array('B', 'KB', 'MB', 'GB'); 
	}
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 

?>