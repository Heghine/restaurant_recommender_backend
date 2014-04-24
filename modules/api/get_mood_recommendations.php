<?php 

$current_user_id = UserManager::getInstance()->getCurrentUserId();

$output = array();
if (isset($_REQUEST['type'])) {
	$type = $_REQUEST['type'];

	if ($type == UserManager::MOOT_TYPE_COFFEE) {
		$output["recommendations"] = UserManager::getInstance()->getCoffeeMoodRecommendations();
	}
}

echo json_encode($output);

?>