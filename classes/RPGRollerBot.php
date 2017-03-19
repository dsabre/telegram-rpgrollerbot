<?php

class RPGRollerBot{
	
	const START_COMMAND = '/start';
	const START_MESSAGE = 'Let the games begin! 😏';
	const LAUNCH_PATTERN = '/^((\d+d\d+|\d)\+?){1,}$/';
	const DICE_PATTERN = '/^\d+d\d+$/';
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
		if(preg_match(self::LAUNCH_PATTERN, $this->text)){
			$response = $this->launchCommand();
		}
		elseif(preg_match('/^\\' . self::START_COMMAND . '/', $this->text)){
			$response = self::START_MESSAGE;
		}
		else{
			$response = $this->notUnderstandCommand();
		}
		
		return array(
			'chat_id'      => $this->chatId,
			'text'         => $response,
			'parse_mode'   => self::PARSE_MODE,
			'method'       => 'sendMessage',
			'reply_markup' => $this->getKeyboard()
		);
	}
	
	/**
	 * Return the keyboard to use
	 * @return string
	 *
	 * @author Daniele Sabre 17/mar/2017
	 */
	private function getKeyboard(){
		return json_encode([
			'keyboard'          => [
				['1d3', '1d4', '1d6'],
				['1d8', '1d10', '1d100'],
				['1d20']
			],
			'one_time_keyboard' => false,
			'resize_keyboard'   => true,
		]);
	}
	
	/**
	 * Perform launch command
	 *
	 * @return string
	 */
	private function launchCommand(){
		$response = null;
		$rolls = explode('+', $this->text);
		$results = [];
		
		// cycle on items to get the results
		foreach($rolls as $roll){
			// check if is a dice roll
			if(preg_match(self::DICE_PATTERN, $roll)){
				$results[] = $this->rollDice($roll);
			}
			else{
				// this is not a dice roll, then insert the value directly on
				// the results array
				$results[] = (int)$roll;
			}
		}
		
		// obtain the result total
		$total = array_sum($results);
		
		// if there's more than one roll then create a string with all the
		// values
		if(count($rolls) > 1){
			$response = implode('+', $results) . ' = <b>' . $total . '</b>';
		}
		else{
			$response = 'Result is: <b>' . $total . '</b>';
			
			// get the roll info
			$info = $this->getRollInfo($rolls[0]);
			
			$maxValue = $info['countLaunches'] * $info['diceType'];
			
			// if i have only one roll insert some flavour texts if necessary
			if($total == $maxValue && $info['diceType'] != 20){
				$response .= sprintf('!%sExcellent! 😏', PHP_EOL);
			}
			elseif($total == $maxValue && $info['diceType'] == 20){
				$response .= sprintf('!!%sYou underestimate my power! 😎', PHP_EOL);
			}
		}
		
		return $response;
	}
	
	/**
	 * Roll a dice
	 *
	 * @param string $roll
	 *
	 * @return int
	 */
	private function rollDice($roll){
		$total = 0;
		$info = $this->getRollInfo($roll);
		
		for($i = 0; $i < $info['countLaunches']; $i++){
			$result = (int)rand(1, $info['diceType']);
			$results[] = $result;
			
			$total += $result;
		}
		
		return $total;
	}
	
	/**
	 * Return the roll info from string
	 *
	 * @param string $roll
	 *
	 * @return array
	 *
	 * @author Daniele Sabre 16/mar/2017
	 */
	private function getRollInfo($roll){
		$info = explode('d', $roll);
		
		return [
			'countLaunches' => (int)$info[0],
			'diceType'      => (int)$info[1]
		];
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
