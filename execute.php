<?php

require_once 'classes/RPGRollerBot.php';

$input = file_get_contents("php://input");
//$input = '{"update_id":364632602,"message":{"message_id":85,"from":{"id":30343769,"first_name":"Daniele","last_name":"Sabre","username":"Wuzzifuzz"},"chat":{"id":30343769,"first_name":"Daniele","last_name":"Sabre","username":"Wuzzifuzz","type":"private"},"date":1489682835,"text":"1d4"}}';

try{
	$rpgrollerbot = new RPGRollerBot($input);
	
	header("Content-Type: application/json");
	
	$parameters = $rpgrollerbot->execCommand();
	
	echo json_encode($parameters);
}
catch(\Exception $exc){
	exit;
}
