<?php
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if(!$update){
	exit;
}

$message = isset($update['message']) ? $update['message'] : "";
$messageId = isset($message['message_id']) ? $message['message_id'] : "";
$chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
$firstname = isset($message['chat']['first_name']) ? $message['chat']['first_name'] : "";
$lastname = isset($message['chat']['last_name']) ? $message['chat']['last_name'] : "";
$username = isset($message['chat']['username']) ? $message['chat']['username'] : "";
$date = isset($message['date']) ? $message['date'] : "";
$text = isset($message['text']) ? $message['text'] : "";

$text = trim($text);
$text = strtolower($text);

$response = '';

// launch command
if(preg_match('/^\/launch/', $text)){
	$text = str_replace('/launch', '', $text);
	$text = trim($text);
	$info = explode('d', $text);
	$countLaunches = (int)$info[0];
	$diceType = (int)$info[1];
	
	$results = [];
	$total = 0;
	for($i = 0; $i < $countLaunches; $i++){
		$result = (int)rand(1, $diceType);
		$results[] = $result;
		
		$total += $result;
	}
	
	$results = implode(', ', $results);
	$response = sprintf("Results: %s%sTotal: %s" , $results, PHP_EOL, $total);
}

header("Content-Type: application/json");
$parameters = array('chat_id' => $chatId, "text" => $response);
$parameters["method"] = "sendMessage";
echo json_encode($parameters);
