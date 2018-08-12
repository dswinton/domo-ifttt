<?php

//Protocol, IP/hostname and Port for Domoticz
define('DOMO_SERVER', 'http://127.0.0.1:9090');

//Super secret password
define('PASSKEY', 'superSecretPasswordOnlyIFTTknows');

if($_REQUEST['passkey'] <> PASSKEY){
	logLine('Invalid passkey');
	exit;
};

function logLine($line){
	syslog(LOG_INFO, '[domoIfttt] '.$line);
	echo '<li>'.$line.'</li>';
};

//Ping the domoticz API
function domoApi($query){
	$apiUrl = DOMO_SERVER.'/json.htm?'.$query;
	$json = file_get_contents($apiUrl);
	$array = json_decode($json, true);
	return $array;
};

//Toogle a device on or off
function domoToggle($idx, $onOff = 'On'){
	$query = 'param=switchlight&type=command&idx='.$idx.'&switchcmd='.$onOff;
	return domoApi($query);
};

//Grab a list of devices from domoticz
//Returns as an array of [simplifiedDeviceName] = idx
function getDevices(){
	$query = 'type=devices&used=true&filter=all&favorite=1';
	$devArray = domoApi($query);
	foreach($devArray['result'] as $d){
		$res[simplifyMatch($d['Name'])] = $d['idx'];
	};
	logLine('Got '.count($res).' devices from Domoticz');
	return $res;
};

//Smish string down into just letters and numbers
//This improves the changes of a proper match by ignoring spaces, punctuation, capitalisation, etc.
function simplifyMatch($v){
	$v = preg_replace("/^[a-zA-Z0-9]+$/", '', $v);
	$v = str_replace(' ', '', $v);
	$v = strtolower($v);
	return $v;
};

//Get device name
if($_REQUEST['devName']){
	$requestedDevice = simplifyMatch($_REQUEST['devName']);
	logLine('Got requested device name: '.$requestedDevice);
}else{
	logLine('No device name requested.');
	exit;
};

//Check for a match in Domoticz
$deviceList = getDevices();
$idx = $deviceList[$requestedDevice];
if($idx){
	logLine('Matched on device IDX '.$idx);
}else{
	logLine('Device not found');
	exit;
};

//Change the state
$requestedState = (int)$_REQUEST['devState'];
if($requestedState){
	$toggleRes = domoToggle($idx, 'On');
}else{
	$toggleRes = domoToggle($idx, 'Off');
};

if($toggleRes['status'] == 'OK'){
	logLine('All OK - job done');
}else{
	logLine('Got error from Domoticz.  Status:'.$toggleRes['status']);
};



?>