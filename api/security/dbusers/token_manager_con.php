<?php
$servername="localhost";
$username="root";
$password="";
$dbname="myhostelapp";

$con=NULL;
try{
	$con=new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	$con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

	$stmtCheckTokenExists=$con->prepare("SELECT * FROM tokens_table where user_id=:var_user_id LIMIT 1");
	$stmtCheckTokenExists->bindParam(':var_user_id', $GLOBALS['args']['user_id']);

	$stmtUpdateToken=$con->prepare("UPDATE tokens_table SET access_token =:var_access_token where user_id=:var_user_id LIMIT 1");
	$stmtUpdateToken->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtUpdateToken->bindParam(':var_access_token', $GLOBALS['args']['access_token']);

	$stmtInsertToken=$con->prepare("INSERT INTO tokens_table values(:var_user_id,:var_access_token, 0)");
	$stmtInsertToken->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtInsertToken->bindParam(':var_access_token', $GLOBALS['args']['access_token']);


	$stmtCheckToken=$con->prepare("SELECT * FROM tokens_table where user_id=:var_user_id AND access_token=:var_access_token LIMIT 1");
	$stmtCheckToken->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtCheckToken->bindParam(':var_access_token', $GLOBALS['args']['access_token']);

	$stmtisUserBlocked=$con->prepare("SELECT * FROM block_list where user_id=:var_user_id LIMIT 1");
	$stmtisUserBlocked->bindParam(':var_user_id', $GLOBALS['args']['user_id']);


}catch(PDOException $e){
	echo "Connection Failed".$e;
}



?>
