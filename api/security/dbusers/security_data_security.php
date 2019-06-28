<?php
$servername="localhost";
$username="root";
$password="";
$dbname="myhostelapp";

$con=NULL;
try{
	$con=new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	$con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

	$stmtAuthentication=$con->prepare("SELECT * FROM security_data where user_id=:var_user_id AND pass=:var_pass AND active=1 LIMIT 1");
	$stmtAuthentication->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtAuthentication->bindParam(':var_pass', $GLOBALS['args']['pass']);

	$stmtCheckUseridExists=$con->prepare("SELECT user_id FROM user_data where user_id=:var_user_id LIMIT 1");
	$stmtCheckUseridExists->bindParam(':var_user_id', $GLOBALS['args']['user_id']);

	$stmtgetUserData=$con->prepare("SELECT user_data.user_id,user_data.user_type,user_data.gender,user_data.name,user_data.hostel_id,user_data.img,user_data.email,user_data.phone,user_data.status, hostels.name AS 'hostel_name' FROM user_data INNER JOIN hostels ON user_data.hostel_id=hostels.id WHERE user_data.user_id=:var_user_id LIMIT 1");
	$stmtgetUserData->bindParam(':var_user_id', $GLOBALS['args']['user_id']);


	}catch(PDOException $e){
		echo "Connection Failed".$e;
	}

	?>
