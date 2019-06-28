<?php
$servername="localhost";
$username="root";
$password="";
$dbname="myhostelapp";

$con=NULL;
try{
	$con=new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	$con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

	$stmtCheckToken=$con->prepare("SELECT * FROM tokens_table where user_id=:var_user_id AND access_token=:var_access_token LIMIT 1");
	$stmtCheckToken->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtCheckToken->bindParam(':var_access_token', $GLOBALS['args']['access_token']);

	$stmtCheckFCMToken=$con->prepare("SELECT * FROM cloud_messaging where user_id=:var_user_id LIMIT 1");
	$stmtCheckFCMToken->bindParam(':var_user_id', $GLOBALS['args']['user_id']);

	$stmtupdateFCMToken=$con->prepare("UPDATE cloud_messaging SET token =:var_fcm_token where user_id=:var_user_id LIMIT 1");
	$stmtupdateFCMToken->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtupdateFCMToken->bindParam(':var_fcm_token', $GLOBALS['args']['fcm_token']);

	$stmtinsertFCMToken=$con->prepare("INSERT INTO cloud_messaging values(:var_user_id,:var_fcm_token, 1)");
	$stmtinsertFCMToken->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtinsertFCMToken->bindParam(':var_fcm_token', $GLOBALS['args']['fcm_token']);

	$stmtupdateServiceAction=$con->prepare("UPDATE cloud_messaging SET service_action =:var_service_action where user_id=:var_user_id LIMIT 1");
	$stmtupdateServiceAction->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtupdateServiceAction->bindParam(':var_service_action', $GLOBALS['args']['service_action']);

}catch(PDOException $e){
	echo "Connection Failed".$e;
}



?>
