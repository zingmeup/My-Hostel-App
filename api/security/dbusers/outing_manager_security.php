<?php
$servername="localhost";
$username="root";
$password="";
$dbname="myhostelapp";

$con=NULL;
try{
	$con=new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	$con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

	$stmtHostelInfo=$con->prepare("SELECT status,outing_type AS type, gender FROM user_data");
	#$stmtHostelInfo=$con->prepare("SELECT active_outing.status,active_outing.type, user_data.gender FROM active_outing INNER JOIN user_data ON active_outing.user_id=user_data.user_id");

	$stmtgetOuting=$con->prepare("SELECT active_outing.*, user_data.bonus, user_data.gender, user_data.name, hostels.name AS hostel_name FROM active_outing INNER JOIN user_data ON active_outing.user_id=user_data.user_id INNER JOIN hostels ON active_outing.hostel_id=hostels.id WHERE hash=:var_hash LIMIT 1");
	$stmtgetOuting->bindParam(':var_hash', $GLOBALS['args']['hash']);

	$stmtsetOutingOut=$con->prepare("UPDATE active_outing SET sign_sec_out=:var_user_id, dt_out=:var_dt_out, status=3 WHERE hash=:var_hash LIMIT 1");
	$stmtsetOutingOut->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtsetOutingOut->bindParam(':var_hash', $GLOBALS['args']['hash']);
	$stmtsetOutingOut->bindParam(':var_dt_out', $GLOBALS['args']['dt_out']);

	$stmtsetUserOut=$con->prepare("UPDATE user_data SET status=3 WHERE user_id=:var_hosteler_userid LIMIT 1");
	$stmtsetUserOut->bindParam(':var_hosteler_userid', $Outing['user_id']);

	$stmtsetOutingIn=$con->prepare("UPDATE active_outing SET sign_sec_in=:var_user_id, dt_in=:var_dt_in, status=4 WHERE hash=:var_hash LIMIT 1");
	$stmtsetOutingIn->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtsetOutingIn->bindParam(':var_hash', $GLOBALS['args']['hash']);
	$stmtsetOutingIn->bindParam(':var_dt_in', $Outing['dt_in']);

	$stmtsetUserIn=$con->prepare("UPDATE user_data SET status=0, otp=NULL, outing_type=NULL, bonus=:var_bonus WHERE user_id=:var_hosteler_userid LIMIT 1");
	$stmtsetUserIn->bindParam(':var_hosteler_userid', $Outing['user_id']);
	$stmtsetUserIn->bindParam(':var_bonus', $Outing['bonus']);

	$stmtmoveToPassive=$con->prepare("INSERT INTO passive_outing VALUES( '', :var_type, :var_user_id, :var_status, :var_place, :var_purpose, :var_going_with, :var_phone, :var_parent_no, :var_hostel_id, :var_dt_apply, :var_dt_exp_out, :var_d_exp_in, :var_dt_in, :var_dt_out, :var_sign_sec_in, :var_sign_sec_out, :var_sign_warden, :var_remarks, :var_late)");
	$stmtmoveToPassive->bindParam(':var_type', $Outing['type']);
	$stmtmoveToPassive->bindParam(':var_user_id', $Outing['user_id']);
	$stmtmoveToPassive->bindParam(':var_status', $Outing['status']);
	$stmtmoveToPassive->bindParam(':var_place', $Outing['place']);
	$stmtmoveToPassive->bindParam(':var_purpose', $Outing['purpose']);
	$stmtmoveToPassive->bindParam(':var_going_with', $Outing['going_with']);
	$stmtmoveToPassive->bindParam(':var_phone', $Outing['phone']);
	$stmtmoveToPassive->bindParam(':var_parent_no', $Outing['parent_no']);
	$stmtmoveToPassive->bindParam(':var_hostel_id', $Outing['hostel_id']);
	$stmtmoveToPassive->bindParam(':var_dt_apply', $Outing['dt_apply']);
	$stmtmoveToPassive->bindParam(':var_dt_exp_out', $Outing['dt_exp_out']);
	$stmtmoveToPassive->bindParam(':var_d_exp_in', $Outing['d_exp_in']);
	$stmtmoveToPassive->bindParam(':var_dt_in', $Outing['dt_in']);
	$stmtmoveToPassive->bindParam(':var_dt_out', $Outing['dt_out']);
	$stmtmoveToPassive->bindParam(':var_sign_sec_in', $Outing['sign_sec_in']);
	$stmtmoveToPassive->bindParam(':var_sign_sec_out', $Outing['sign_sec_out']);
	$stmtmoveToPassive->bindParam(':var_sign_warden', $Outing['sign_warden']);
	$stmtmoveToPassive->bindParam(':var_remarks', $Outing['remarks']);
	$stmtmoveToPassive->bindParam(':var_late', $GLOBALS['LATE']);

	$stmtdeleteOuting=$con->prepare("DELETE FROM active_outing WHERE hash=:var_hash LIMIT 1");
	$stmtdeleteOuting->bindParam(':var_hash', $GLOBALS['args']['hash']);




}catch(PDOException $e){
	echo "Connection Failed".$e;
}
