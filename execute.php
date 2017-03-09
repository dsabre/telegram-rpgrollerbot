<?php

require_once 'classes/RPGRollerBot.php';

$input = file_get_contents("php://input");
$input = '{"update_id":364632588,"message":{"message_id":56,"from":{"id":30343769,"first_name":"Daniele","last_name":"Sabre","username":"Wuzzifuzz"},"chat":{"id":30343769,"first_name":"Daniele","last_name":"Sabre","username":"Wuzzifuzz","type":"private"},"date":1488908870,"text":"/start"}}';
$input = '';

try{
	$rpgrollerbot = new RPGRollerBot($input);
	
	header("Content-Type: application/json");
	
	$parameters = $rpgrollerbot->execCommand();
	$parameters["method"] = "sendMessage";
	
	echo json_encode($parameters);
}
catch(\Exception $exc){
	exit;
}
