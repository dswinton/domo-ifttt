<?php

// *** Change settings for your Domoticz Server here:
// Protocol, IP/hostname and Port for Domoticz
define('DOMO_SERVER', 'http://127.0.0.1:9090');

// *** Change settings for your Secret password here:
// This password is used in the IFTTT Applets.
define('PASSKEY', 'superSecretPasswordOnlyIFTTknows');

// *** DO NOT EDIT PASSED THIS LINE ***
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

//Toogle state for a device, group, or scene
function domoToggle($idx, $grouptype, $onOff = 'On'){
        if ($grouptype == 'Group')
                $query = 'param=switchscene&type=command&idx='.$idx.'&switchcmd='.$onOff;
        elseif ($grouptype == 'Scene')
		// Scenes can only be toggled ON
                $query = 'param=switchscene&type=command&idx='.$idx.'&switchcmd=On';
        else
		$query = 'param=switchlight&type=command&idx='.$idx.'&switchcmd='.$onOff;
	return domoApi($query);
};

//Search through favorites devices from domoticz
//Returns as an multi array with only our device (Name, idx, Type)
function getDevices($requestedDevice){
        $query = 'type=devices&used=true&filter=all&favorite=1';
        $devArray = domoApi($query);
        $devicelisting = array();
        foreach($devArray['result'] as $d){
                if ($requestedDevice == simplifyMatch($d['Name'])) {
                        $devicelisting[] = array('name'=> $d['Name'],'idx' => $d['idx'], 'type' => $d['Type']);
                        break;
                }
        };
        return $devicelisting;
};

//Smish string down into just letters and numbers
//This improves the changes of a proper match by ignoring spaces, punctuation, capitalisation, "the", "my" etc.
function simplifyMatch($v){
	$v = strtolower($v);
	$v = removeUselessWord($v, 'the');
	$v = removeUselessWord($v, 'my');
	$v = removeUselessWord($v, 'our');
// Problem: Below 2 lines created spaces result in zero results.
// Have to cleanup these 2 lines in the future.
//      $v = preg_replace("/^[a-zA-Z0-9]+$/", '', $v);
//      $v = str_replace(' ', '', $v);
	return $v;
};

function removeUselessWord($v, $w){
	$len = strlen($w)+1;
	if(substr($v,0,$len) == $w.' '){return substr($v,$len);};
	return $v;
};

//Get device name
if($_REQUEST['devName']){
// Display original input instead of filtered one in original script.
        logLine('Got request for device name: '.$_REQUEST['devName']);
        $requestedDevice = simplifyMatch($_REQUEST['devName']);
}else{
	logLine('No device name requested.');
	exit;
};

//Check for a match in Domoticz
// Changed to include support for multi array group and scenes
$deviceList = getDevices($requestedDevice);
$idx = $deviceList[0]['idx'];
if($idx){
        logLine('Matched on device Name: '.$deviceList[0]['name']);
        logLine('Matched on device IDX:  '.$deviceList[0]['idx']);
        logLine('Matched on device Type: '.$deviceList[0]['type']);
}else{
	logLine('Device not found');
	exit;
};

//Execute the switching
$requestedState = (int)$_REQUEST['devState'];
if($requestedState){
// Changed to work with multi array results
        $toggleRes = domoToggle($idx,$deviceList[0]['type'],'On');
}else{
// Changed to work with multi array results
        $toggleRes = domoToggle($idx,$deviceList[0]['type'],'Off');
};

if($toggleRes['status'] == 'OK'){
	logLine('All OK - job done');
}else{
	logLine('Got error from Domoticz.  Status:'.$toggleRes['status']);
};
?>
