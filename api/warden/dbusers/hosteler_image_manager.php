<?php
$servername="localhost";
$username="root";
$password="";
$dbname="myhostelapp";

$con=NULL;
try{
	$con=new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	$con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

	$stmtgetDetails=$con->prepare("SELECT active_outing.user_id, user_data.img FROM active_outing INNER JOIN user_data ON active_outing.user_id=user_data.user_id WHERE active_outing.hash=:var_hash LIMIT 1");
	$stmtgetDetails->bindParam(':var_hash', $GLOBALS['args']['hash']);


}catch(PDOException $e){
	echo "Connection Failed".$e;
}



/*
$stmtCheckStatus=$con->prepare("SELECT user_id, status, outing_type, bonus, otp, gender, hostel_id FROM user_data where user_id=:var_user_id LIMIT 1");
$stmtCheckStatus->bindParam(':var_user_id', $GLOBALS['args']['user_id']);


$stmtuniqueHashInOuting=$con->prepare("SELECT hash FROM active_outing where hash=:var_hash LIMIT 1");
$stmtuniqueHashInOuting->bindParam(':var_hash', $hash);


$stmtmakeOutingRequest=$con->prepare("INSERT INTO active_outing VALUES( :var_hash, :var_otp,  :var_type, :var_status, :var_user_id, :var_place, :var_purpose, :var_going_with, :var_phone,  :var_parent_phone, :var_hostel_id, :var_dt_apply, :var_dt_exp_out, :var_d_exp_in,NULL,NULL,NULL,NULL,NULL,NULL)");
$stmtmakeOutingRequest->bindParam(':var_hash',  $generateOTP['hash']);
$stmtmakeOutingRequest->bindParam(':var_otp', $generateOTP['otp']);
$stmtmakeOutingRequest->bindParam(':var_type',  $generateOTP['outing_type']);
$stmtmakeOutingRequest->bindParam(':var_status', $status);
$stmtmakeOutingRequest->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
$stmtmakeOutingRequest->bindParam(':var_place', $GLOBALS['args']['place']);
$stmtmakeOutingRequest->bindParam(':var_purpose', $GLOBALS['args']['purpose']);
$stmtmakeOutingRequest->bindParam(':var_going_with', $GLOBALS['args']['going_with']);
$stmtmakeOutingRequest->bindParam(':var_phone', $GLOBALS['args']['phone']);
$stmtmakeOutingRequest->bindParam(':var_parent_phone', $GLOBALS['args']['parent_phone']);//
$stmtmakeOutingRequest->bindParam(':var_hostel_id', $GLOBALS['checkStatus']['hostel_id']);
$stmtmakeOutingRequest->bindParam(':var_dt_apply', $dt_apply);
$stmtmakeOutingRequest->bindParam(':var_dt_exp_out', $GLOBALS['args']['dt_exp_out']);//
$stmtmakeOutingRequest->bindParam(':var_d_exp_in', $GLOBALS['args']['d_exp_in']);//


$stmtupdateUserStatusToAccepted=$con->prepare("UPDATE user_data SET status ='2', outing_type=:var_type, otp=:var_otp where user_id=:var_user_id LIMIT 1");
$stmtupdateUserStatusToAccepted->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
$stmtupdateUserStatusToAccepted->bindParam(':var_type',  $generateOTP['outing_type']);
$stmtupdateUserStatusToAccepted->bindParam(':var_otp', $generateOTP['otp']);

$stmtupdateUserStatusToRequested=$con->prepare("UPDATE user_data SET status ='1', outing_type=:var_type, otp=:var_otp where user_id=:var_user_id LIMIT 1");
$stmtupdateUserStatusToRequested->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
$stmtupdateUserStatusToRequested->bindParam(':var_type',  $generateOTP['outing_type']);
$stmtupdateUserStatusToRequested->bindParam(':var_otp', $generateOTP['otp']);

$stmtgetOuting=$con->prepare("SELECT * FROM active_outing where hash=:var_hash AND user_id=:var_user_id LIMIT 1");
$stmtgetOuting->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
$stmtgetOuting->bindParam(':var_hash', $GLOBALS['args']['hash']);

$cancelOutingRequest=$con->prepare("DELETE FROM active_outing where hash=:var_hash AND user_id=:var_user_id LIMIT 1");
$cancelOutingRequest->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
$cancelOutingRequest->bindParam(':var_hash', $GLOBALS['args']['hash']);

$stmtsetStatusToNull=$con->prepare("UPDATE user_data SET status =0, outing_type=NULL, otp=NULL where user_id=:var_user_id LIMIT 1");
$stmtsetStatusToNull->bindParam(':var_user_id', $GLOBALS['args']['user_id']);

$stmtOutingHistory=$con->prepare("SELECT * FROM passive_outing WHERE user_id=:var_user_id LIMIT 20");
$stmtOutingHistory->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
*/

?>
