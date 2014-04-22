<?php 

final class UserItemManager {
	const PREFERRED_ITEM_RATING_TRESHOLD = 1;
	const MAXIMUM_ITEM_RATING = 5;
	const MINIMUM_ITEM_RATING = 1;
	
	protected static $_instance;
	
	private function __construct() {}
	
	private function __clone() {}
	
	public static function getInstance() {
		if (self::$_instance == null) {
			self::$_instance = new self();
		}
	
		return self::$_instance;
	}
	
	public function getUserPreferredItemIds($user_id) {
		global $config;
		$result = dbQuery("SELECT * FROM user_item_rating WHERE user_id=$user_id", $user_id);
		
		$output = array();
		foreach ($result as $r) {
			if ($r->rating >= UserItemManager::PREFERRED_ITEM_RATING_TRESHOLD) {
				$output[] = $r->item_id;
				if ($config['print_enabled'] == 1) {
					echo "item_id = " . $r->item_id . " <br>";
				}
			}
		}
		
		return $output;
	}
	
	public function getUserPreferredItems($user_id) {
		$result = dbQuery("SELECT * FROM user_item_rating WHERE user_id=$user_id", $user_id);
	
		$output = array();
		foreach ($result as $r) {
			if ($r->rating >= UserItemManager::PREFERRED_ITEM_RATING_TRESHOLD) {
				$output[] = array('item_id' => $r->item_id, 'rating' => $r->rating);
			}
		}
	
		return $output;
	}
	
	public function addUserItemRating($user_id, $item_id, $rating) {
		dbQuery("INSERT INTO user_item_rating(user_id,item_id,rating) VALUES($user_id, $item_id, $rating);", $user_id);
	}
	
	public function getAllItemIds() {
		$output = array();
		$result = dbQuery("SELECT item_id FROM item", 0);
	
		foreach ($result as $item_id) {
			$output[] = $item_id->item_id;
		}
	
		return $output;
	}
	
	public function getAllItemFbIds() {
		$output = array();
		$result = dbQuery("SELECT item_fb_id FROM item", 0);
		
		foreach ($result as $item_id) {
			$output[] = $item_id->item_fb_id;
		}
		
		return $output;
	}

	public function getAllItems() {
		$output = array();
		$result = dbQuery("SELECT * FROM item", 0);
		
		$temp = array();
		foreach ($result as $item) {
			$temp['item_id'] = $item->item_id;
			$temp['item_fb_id'] = $item->item_fb_id;
			$temp['name'] = $item->name;
			$temp['address'] = $item->address;
			$temp['working_hours'] = $item->working_hours;
			$temp['type'] = $item->type;
			
			$output[] = $temp;
		}
		
		return $output;
	}
	
	public function getItemByFbId($item_fb_id) {
		$result = dbQuery("SELECT * FROM item", 0);
		$output = array();
		foreach ($result as $r) {
			if ($r->item_fb_id == $item_fb_id) {
				$output['item_id'] = $r->item_id;
				$output['item_fb_id'] = $r->item_fb_id;
				$output['name'] = $r->name;
				$output['address'] = $r->address;
				$output['working_hours'] = $r->working_hours;
				$output['type'] = $r->type;
				
				break;
			}
		}
		
		return $output;
	}
	
	public function addItem($item) {
		$fields_str = "(";
		$values_str = "(";
		foreach ($item as $key=>$value) {
			$fields_str .= $key . ",";
			$values_str .= "'".$value . "',";
		}
		$fields_str = substr_replace($fields_str, ")", strlen($fields_str) - 1, 1);
		$values_str = substr_replace($values_str, ")", strlen($values_str) - 1, 1);
		
		$query = "INSERT INTO item" . $fields_str . " VALUES" . $values_str .";";
		dbQuery($query, 0); 
		$item_id = mysql_insert_id();
		
		return $item_id;
	}
}
?>