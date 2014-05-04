<?php 
// ItemSimilarityManager::getInstance()->updateItemItemSimilarity();
// $result = UserItemManager::getInstance()->getUserPreferredItemIds(3);
// ItemBasedAlgorithm::getInstance()->getTopNRecommendations(3, $result);

$ratings = UserItemManager::getInstance()->constructUserItemMatrix();

// ItemBasedAlgorithm::getInstance()->getItemRatingPrediction(8,1);
// $items = UserItemManager::getInstance()->getAllItemIds();

// foreach ($items as $item) {
// 	UserItemManager::getInstance()->updateItemRating($item);
// }


$items = UserItemManager::getInstance()->getAllItemIds();
$users = array(1,2,3,4,5,6,7,8,9,10,11);
$predicted_ratings = array();
$i = 0; $j = 0;
foreach ($users as $u) {
// 	echo "user_id = " .$u."<br>";
	$j = 0;
	foreach ($items as $item) {
		
		$rating = ItemBasedAlgorithm::getInstance()->getItemRatingPrediction($item,$u);
		$predicted_ratings[$i][$j] = $rating;
		
		$j++; 
// 		echo "item_id = " . $item . " ; predicted_rating = " . $rating . "<br>";
	}
	$i++;
}

echo "<br>";echo "<br>";
for ($i = 0; $i < count($users); $i++) {
	for ($j = 0; $j < count($items); $j++) {
		echo $ratings[$i][$j] . " --- " . $predicted_ratings[$i][$j] . " | ";
	}
	echo "<br>";
}

?>