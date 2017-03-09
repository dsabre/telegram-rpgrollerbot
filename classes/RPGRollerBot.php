<?php

class RPGRollerBot{
	
	const START_COMMAND = '/start';
	const LAUNCH_COMMAND = '/launch';
	const PARSE_MODE = 'HTML';
	
	/**
	 * @var string
	 */
	private $input;
	
	/**
	 * @var string
	 */
	private $message;
	
	/**
	 * @var string
	 */
	private $messageId;
	
	/**
	 * @var string
	 */
	private $chatId;
	
	/**
	 * @var string
	 */
	private $firstname;
	
	/**
	 * @var string
	 */
	private $lastname;
	
	/**
	 * @var string
	 */
	private $username;
	
	/**
	 * @var string
	 */
	private $date;
	
	/**
	 * @var string
	 */
	private $text;
	
	/**
	 * RPGRollerBot constructor.
	 *
	 * @param string $input
	 */
	public function __construct($input){
		$this->input = $input;
		
		$this->parseData();
	}
	
	/**
	 * Parse the input data
	 *
	 * @throws Exception
	 */
	private function parseData(){
		$update = json_decode($this->input, true);
		
		if(!$update){
			throw new Exception('No data');
		}
		
		$this->message = isset($update['message']) ? $update['message'] : "";
		$this->messageId = isset($this->message['message_id']) ? $this->message['message_id'] : "";
		$this->chatId = isset($this->message['chat']['id']) ? $this->message['chat']['id'] : "";
		$this->firstname = isset($this->message['chat']['first_name']) ? $this->message['chat']['first_name'] : "";
		$this->lastname = isset($this->message['chat']['last_name']) ? $this->message['chat']['last_name'] : "";
		$this->username = isset($this->message['chat']['username']) ? $this->message['chat']['username'] : "";
		$this->date = isset($this->message['date']) ? $this->message['date'] : "";
		$this->text = isset($this->message['text']) ? $this->message['text'] : "";
		
		$this->text = trim($this->text);
		$this->text = strtolower($this->text);
	}
	
	/**
	 * Execute the commands
	 *
	 * @return array
	 * @throws Exception
	 */
	public function execCommand(){
		if(preg_match('/^\\' . self::LAUNCH_COMMAND . '/', $this->text)){
			$response = $this->launchCommand();
		}
		elseif(preg_match('/^\\' . self::START_COMMAND . '/', $this->text)){
			throw new Exception('Command disabled');
		}
		else{
			$response = $this->notUnderstandCommand();
		}
		
		return array(
			'chat_id'    => $this->chatId,
			'text'       => $response,
			'parse_mode' => self::PARSE_MODE
		);
	}
	
	/**
	 * Perform launch command
	 *
	 * @return string
	 */
	private function launchCommand(){
		$text = str_replace(self::LAUNCH_COMMAND, '', $this->text);
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
				$response = sprintf("Results: %s%sTotal: <b>%s</b>", $results, PHP_EOL, $total);
			}
			else{
				$result = (int)rand(1, $diceType);
				$response = sprintf("Result: <b>%s</b>", $result);
				
				// add some flavour texts
				if($result == $diceType && $diceType != 20){
					$response .= sprintf('!%sExcellent! 😏', PHP_EOL);
				}
				elseif($result == $diceType && $diceType == 20){
					$response .= sprintf('!!%sYou underestimate my power! 😎', PHP_EOL);
				}
			}
		}
		else{
			$response = 'Have you lost your dice? 😆';
		}
		
		return $response;
	}
	
	/**
	 * Perform the "not understand" command
	 *
	 * @return string
	 */
	private function notUnderstandCommand(){
		return 'Sorry, but I did not understand 😕';
	}
	
}