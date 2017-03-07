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
$origText = isset($message['text']) ? $message['text'] : "";

$origText = trim($origText);
$origText = strtolower($origText);

$response = '';

// launch command
if(preg_match('/^\/launch/', $origText)){
	$text = str_replace('/launch', '', $origText);
	$text = trim($text);
	
	if(!empty($text)){
		$info = explode('d', $text);
		$countLaunches = (int)$info[0];
		$diceType = (int)$info[1];
		
		if($countLaunches > 1){
			$results = [];
			$total = 0;
			for($i = 0; $i < $countLaunches; $i++){
				$result = (int)rand(1, $diceType);
				$results[] = $result;
				
				$total += $result;
			}
			
			$results = implode(', ', $results);
			$response = sprintf("Results: %s%sTotal: <b>%s</b>" , $results, PHP_EOL, $total);
		}
		else{
			$result = (int)rand(1, $diceType);
			$response = sprintf("Result: <b>%s</b>" , $result);
			
			// add some flavour texts
			if($result == $diceType && $diceType != 20){
				$response .= '! Excellent! 😏';
			}
			elseif($result == $diceType && $diceType == 20){
				$response .= '!! You underestimate my power! 😎';
			}
		}
	}
	else{
		$response = 'Have you lost your dice? 😆';
	}
}
else{
	// not understand command
	$response = 'Sorry, but I did not understand 😕';
}

header("Content-Type: application/json");
$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => 'HTML');
$parameters["method"] = "sendMessage";
echo json_encode($parameters);
