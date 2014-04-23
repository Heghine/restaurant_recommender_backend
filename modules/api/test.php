<?php 
// ItemSimilarityManager::getInstance()->updateItemItemSimilarity();
// $result = UserItemManager::getInstance()->getUserPreferredItemIds(3);
// ItemBasedAlgorithm::getInstance()->getTopNRecommendations(3, $result);

UserItemManager::getInstance()->constructUserItemMatrix();

// ItemBasedAlgorithm::getInstance()->getItemRatingPrediction(8,1);
// $items = UserItemManager::getInstance()->getAllItemIds();

// foreach ($items as $item) {
// 	UserItemManager::getInstance()->updateItemRating($item);
// }


$items = UserItemManager::getInstance()->getAllItemIds();
foreach ($items as $item) {
	echo "item = " . $item . " ; predicted_rating = " . ItemBasedAlgorithm::getInstance()->getItemRatingPrediction($item,1) . "<br>";
}

?>