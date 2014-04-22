<?php 
require_once 'config.php';
require_once 'db.php';

require_once 'includes/classes/AndroidConnector.php';
require_once 'includes/classes/UserManager.php';
require_once 'includes/classes/UserItemManager.php';

require_once 'includes/item_similarity/ItemSimilarity.php';
require_once 'includes/item_similarity/ItemSimilarityManager.php';

require_once 'includes/item_based_cf/ItemBasedAlgorithm.php';

$connector = new AndroidConnector();

if (isset($_REQUEST['t']) && $_REQUEST['t'] != 'test' && $_REQUEST['t'] != 'compute_item_item_similarity') {
	$current_user_id = $connector->authorize();
} else {
	$current_user_id = 0;
}

if ($current_user_id) {
	UserManager::getInstance()->setCurrentUserId($current_user_id);
}

?>