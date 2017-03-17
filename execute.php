<?php

require_once 'classes/RPGRollerBot.php';

$input = file_get_contents("php://input");

try{
	$rpgrollerbot = new RPGRollerBot($input);
	
	header("Content-Type: application/json");
	
	$parameters = $rpgrollerbot->execCommand();
	
	echo json_encode($parameters);
}
catch(\Exception $exc){
	exit;
}
