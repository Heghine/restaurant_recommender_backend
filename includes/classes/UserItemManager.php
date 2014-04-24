<?php 

final class UserItemManager {
	const PREFERRED_ITEM_RATING_TRESHOLD = 3;
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
	
	public function getUserNotRatedItems($user_id) {
		$items = $this->getAllItemIds();
		$user_preferred_items = $this->getUserPreferredItemIds($user_id);
		
		$output = array();
		foreach ($items as $item) {
			if (!in_array($item, $user_preferred_items)) {
				$output[] = $item;
			}
		}
		
		return $output;
	}
	
	public function setUserPreferredItems($user_preferences, $user_id) {
		$item_fb_ids = $this->getAllItemFbIds();
	
		foreach ($user_preferences as $user_preference) {
			if (in_array($user_preference['fb_id'], $item_fb_ids)) {
				$item = $this->getItemByFbId($user_preference['fb_id']);
			} else {
				$item = array();
				$item['item_fb_id'] = $user_preference['fb_id'];
				$item['name'] = $user_preference['name'];
				$item['address'] = $user_preference['location'];
				$item['working_hours'] = $user_preference['working_hours'];
				$item['type'] = $user_preference['type'];
	
				$item['item_id'] = $this->addItem($item);
			}
				
			$this->addUserItemRating($user_id, $item['item_id'], UserItemManager::MAXIMUM_ITEM_RATING);
		}
	}
	
	public function addUserItemRating($user_id, $item_id, $rating) {
		dbQuery("INSERT INTO user_item_rating(user_id,item_id,rating) VALUES($user_id, $item_id, $rating);", $user_id);
		$this->updateItemRating($item_id);
	}
	
	public function getUserItemRating($user_id, $item_id) {
		$user_items = dbQuery("SELECT * FROM user_item_rating WHERE user_id=$user_id", $user_id);
		
		foreach ($user_items as $item) {
			if ($item->item_id == $item_id) {
				return $item->rating;
			}
		}
		
		return 0;
	}
	
	public function updateItemRating($item_id) {
		$item_ratings = ItemSimilarity::getInstance()->getItemRatings($item_id);
		
		$count = 0;
		$sum = 0;
		foreach ($item_ratings as $item_rating) {
			$count++;
			$sum += $item_rating['rating'];
		}
		if ($count > 0) {
			$item_rating_avg = round($sum / $count, 1);
		} else {
			$item_rating_avg = 1;
		}
		
		dbQuery("UPDATE item SET rating = $item_rating_avg, rating_count = $count WHERE item_id=$item_id", 0);
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
			foreach ($item as $key => $value) {
				$temp[$key] = $value;
			}
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
	
	public function constructUserItemMatrix() {
		$result = dbQuery("SELECT * FROM user_item_rating", 0);
		$users = dbQuery("SELECT user_id FROM user", 0);
		$items = dbQuery("SELECT item_id FROM item", 0);
		
		$ratings = array();
		for ($i = 0; $i < count($users); $i++) {
			for ($j = 0; $j < count($items); $j++) {
				$ratings[$i][$j] = 0;
			}
		}
		
		foreach ($result as $r) {
				$ratings[$r->user_id - 1][$r->item_id - 1] = $r->rating;
		}
		
		echo "x  |  ";
		for ($j = 0; $j < count($items); $j++) {
			echo $items[$j]->item_id . " ";
		}
		echo "<br>";
		for ($i = 0; $i < count($users); $i++) {
			echo $users[$i]->user_id . "  |  ";
			for ($j = 0; $j < count($items); $j++) {
				echo $ratings[$i][$j] . "  ";
			}
			echo "<br>";
		}
	}
}
?>