<?php 

final class UserManager {
	const MOOT_TYPE_COFFEE = 'coffee';
	const MOOT_TYPE_DANCE = 'dance';
	const MOOT_TYPE_SAD = 'sad';
	const MOOT_TYPE_MUSIC = 'music';
	
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
	
	public function getCoffeeMoodRecommendations() {
		$items = UserItemManager::getInstance()->getAllItems();
		
		$output = array();
		foreach ($items as $item) {
			$item_type = strtolower($item['type']);
			if ($item_type == 'cafe' || strpos($item_type, 'cafe')) {
				$output[] = $item;
			}	
		}
		
		return $output;
	}
	
	public function getPredictionRecommendations($user_id) {
		$items = UserItemManager::getInstance()->getUserNotRatedItems();
		
		$item_predictions = array();
		foreach ($items as $item) {
			$predicted_rating = ItemBasedAlgorithm::getInstance()->getItemRatingPrediction($item, $user_id);
			$item_predictions[] =  array('item_id' => $item, 'predicted_rating' => $predicted_rating);
		}
	}
}

?>