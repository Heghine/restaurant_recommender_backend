<?php 

final class ItemSimilarityManager {
	protected static $_instance;
	
	private function __construct() {}
	
	private function __clone() {}
	
	public static function getInstance() {
		if (self::$_instance == null) {
			self::$_instance = new self();
		}
	
		return self::$_instance;
	}
	
	public function calculetItemsSimilarities() {
		global $config;
		$item_ids = UserItemManager::getInstance()->getAllItemIds();
		
		$output = array();
		
		for ($i = 0; $i < count($item_ids); $i++) {
			for ($j = $i + 1; $j < count($item_ids); $j++) {
				$similarity = ItemSimilarity::getInstance()->calculate($item_ids[$i], $item_ids[$j]);
				if ($config['print_enabled'] == 1) {
					echo "item_ids[".$i."] = " . $item_ids[$i]." ; ";
					echo "item_ids[".$j."] = " . $item_ids[$j].  " ; s = ". $similarity ." <br>";
				}
				if ($similarity != 0) {
					$similarity = round($similarity, 2);
					$output[] = array('first_item_id' => $item_ids[$i], 'second_item_id' => $item_ids[$j], 'similarity' => $similarity);
				}
			}
		}
		
		return $output;
	}
	
	public function updateItemItemSimilarity() {
		$result = $this->calculetItemsSimilarities();
		
		$query = "INSERT INTO item_item_similarity";
		
		$fields_str = "(";
		$values_str = "";
		$is_fields_str_ready = false;
		foreach ($result as $o) {
			$values_str .= "(";
			
			foreach ($o as $key => $value) {
				if (!$is_fields_str_ready) {
					$fields_str .= $key . ",";
				}
				$values_str .= $value . ",";
			}
			$values_str = substr_replace($values_str, "),", strlen($values_str) - 1, 1);
			
			if (!$is_fields_str_ready) {
				$is_fields_str_ready = true;
			}
		}
		$fields_str = substr_replace($fields_str, ")", strlen($fields_str) - 1, 1);
		$values_str = substr($values_str, 0, strlen($values_str) - 1);
		$query .= $fields_str . " VALUES" . $values_str . ";";
		
		echo "<br>".$query ."<br>";
		dbQuery($query, 0);
	}
	
	public function getItemItemSimilarity($first_item_id, $second_item_id) {
		$result = dbQuery("SELECT * FROM item_item_similarity", 0);
		
		$similarity = 0;
		foreach ($result as $r) {
			if (($r->first_item_id == $first_item_id && $r->second_item_id == $second_item_id) ||
				($r->first_item_id == $second_item_id && $r->second_item_id == $first_item_id)) {
				$similarity = $r->similarity;
				break;
			}
		}
		
		return $similarity;
	}
}
?>