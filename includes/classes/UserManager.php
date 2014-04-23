<?php 

final class UserManager {
	
	protected static $_instance;
	
	private $current_user_id;
	
	private function __construct() {}
	
	private function __clone() {}
	
	public static function getInstance() {
		if (self::$_instance == null) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	public function getCurrentUserId() {
		return $this->current_user_id;
	}
	
	public function setCurrentUserId($user_id) {
		$this->current_user_id = $user_id;
	}
	
	public function getUserById($user_id) {
		$output = array();
		
		$result = dbQuery("SELECT * FROM user WHERE user_id=$user_id", $user_id);
		$result = $result[0];
		
		$output['user_id'] = $result->user_id;
		$output['user_fb_id'] = $result->user_fb_id;
		$output['first_name'] = $result->first_name;
		$output['last_name'] = $result->last_name;
		$output['gender'] = $result->gender;
		$output['location'] = $result->location;
		
		return $output;
	}
	
	public function getRecommendations($user_id) {
		global $config;
		if ($config['print_enabled'] == 1)
			echo "user preferred items <br>";
		$user_item_ids = UserItemManager::getInstance()->getUserPreferredItemIds($user_id);
		
		$result = ItemBasedAlgorithm::getInstance()->getTopNRecommendations($user_id, $user_item_ids);
		
		return $result;
	}
}

?>