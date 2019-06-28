<?php
$servername="localhost";
$username="root";
$password="";
$dbname="myhostelapp";

$con=NULL;
try{
	$con=new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	$con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

	$stmtCheckUseridExists=$con->prepare("SELECT user_id FROM user_data where user_id=:var_user_id LIMIT 1");
	$stmtCheckUseridExists->bindParam(':var_user_id', $GLOBALS['args']['user_id']);

	$stmtgetUserData=$con->prepare("SELECT user_data.user_id,user_data.user_type,user_data.gender,user_data.name,user_data.hostel_id,user_data.img,user_data.email,user_data.phone,user_data.status, hostels.name AS 'hostel_name' FROM user_data INNER JOIN hostels ON user_data.hostel_id=hostels.id WHERE user_data.user_id=:var_user_id LIMIT 1");
	$stmtgetUserData->bindParam(':var_user_id', $GLOBALS['args']['user_id']);

	$stmtUpdateUser=$con->prepare("UPDATE user_data SET user_id =:var_user_id, gender =:var_gender, branch =:var_branch, course =:var_course, section =:var_section,  name =:var_name, hostel_id =:var_hostel_id, room =:var_room, img =:var_img where user_id=:var_user_id LIMIT 1");
	$stmtUpdateUser->bindParam(':var_user_id', $GLOBALS['getUserInfo']['user_id']);
	$stmtUpdateUser->bindParam(':var_user_type', $GLOBALS['getUserInfo']['user_type']);
	$stmtUpdateUser->bindParam(':var_gender', $GLOBALS['getUserInfo']['gender']);
	$stmtUpdateUser->bindParam(':var_branch', $GLOBALS['getUserInfo']['branch']);
	$stmtUpdateUser->bindParam(':var_course', $GLOBALS['getUserInfo']['course']);
	$stmtUpdateUser->bindParam(':var_section', $GLOBALS['getUserInfo']['section']);
	$stmtUpdateUser->bindParam(':var_name', $GLOBALS['getUserInfo']['name']);
	$stmtUpdateUser->bindParam(':var_hostel_id', $GLOBALS['getUserInfo']['hostel_id']);
	$stmtUpdateUser->bindParam(':var_room', $GLOBALS['getUserInfo']['room']);
	$stmtUpdateUser->bindParam(':var_img', $GLOBALS['getUserInfo']['img']);

	$stmtInsertUser=$con->prepare("INSERT INTO user_data VALUES(:var_user_id, :var_user_type, :var_gender, :var_name, :var_branch, :var_course, :var_section, :var_hostel_id, :var_room, :var_img,NULL,NULL,NULL, 0, NULL, 15, NULL)");
	$stmtInsertUser->bindParam(':var_user_id', $GLOBALS['getUserInfo']['user_id']);
	$stmtInsertUser->bindParam(':var_user_type', $GLOBALS['getUserInfo']['user_type']);
	$stmtInsertUser->bindParam(':var_gender', $GLOBALS['getUserInfo']['gender']);
	$stmtInsertUser->bindParam(':var_name', $GLOBALS['getUserInfo']['name']);
	$stmtInsertUser->bindParam(':var_branch', $GLOBALS['getUserInfo']['branch']);
	$stmtInsertUser->bindParam(':var_course', $GLOBALS['getUserInfo']['course']);
	$stmtInsertUser->bindParam(':var_section', $GLOBALS['getUserInfo']['section']);
	$stmtInsertUser->bindParam(':var_hostel_id', $GLOBALS['getUserInfo']['hostel_id']);
	$stmtInsertUser->bindParam(':var_room', $GLOBALS['getUserInfo']['room']);
	$stmtInsertUser->bindParam(':var_img', $GLOBALS['getUserInfo']['img']);

	$stmtCheckStatus=$con->prepare("SELECT user_id, status, outing_type, bonus, otp, gender, hostel_id FROM user_data where user_id=:var_user_id LIMIT 1");
	$stmtCheckStatus->bindParam(':var_user_id', $GLOBALS['args']['user_id']);

	$stmtHostelInfo=$con->prepare("SELECT status,outing_type, otp, user_id from user_data where hostel_id=(SELECT hostel_id from user_data where user_id=:var_user_id LIMIT 1) AND user_type='S'");
	$stmtHostelInfo->bindParam(':var_user_id', $GLOBALS['args']['user_id']);

	$stmtHostelData=$con->prepare("SELECT user_id,name,room,branch,course,email,phone,parent_no,outing_type,status FROM user_data WHERE hostel_id=(SELECT hostel_id FROM user_data WHERE user_id=:var_user_id LIMIT 1)  AND user_type='S'");
	$stmtHostelData->bindParam(':var_user_id', $GLOBALS['args']['user_id']);

	$stmtHostelDataWithOuting=$con->prepare("SELECT user_data.user_id,user_data.name, user_data.room,user_data.branch, user_data.course, user_data.email, user_data.phone, user_data.parent_no,user_data.outing_type, active_outing.*
		FROM user_data INNER JOIN active_outing ON user_data.user_id=active_outing.user_id
		WHERE user_data.hostel_id=(SELECT hostel_id FROM user_data WHERE user_id=:var_user_id LIMIT 1)  AND user_type='S'");
	$stmtHostelDataWithOuting->bindParam(':var_user_id', $GLOBALS['args']['user_id']);


	}catch(PDOException $e){
		echo "Connection Failed".$e;
	}

	?>
