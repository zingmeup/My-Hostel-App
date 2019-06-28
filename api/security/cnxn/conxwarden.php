<?php
$servername="localhost";
$username="root";
$password="";
$dbname="myhostelapp";

$con=NULL;
try{
	$con=new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	$con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

	$stmtgetCurrentLeave=$con->prepare("SELECT * FROM active_leave where hostel=:var_hostel_id AND status='3'");
	$stmtgetCurrentLeave->bindParam(':var_hostel_id', $GLOBALS['args']['hostel_id']);


	$stmtgetCurrentDayout=$con->prepare("SELECT * FROM active_dayout where hostel=:var_hostel_id AND status='3'");
	$stmtgetCurrentDayout->bindParam(':var_hostel_id', $GLOBALS['args']['hostel_id']);

	$stmtgetLeaveRequests=$con->prepare("SELECT * FROM active_leave where hostel=:var_hostel_id AND status='1'");
	$stmtgetLeaveRequests->bindParam(':var_hostel_id', $GLOBALS['args']['hostel_id']);


	$stmtupdateUserStatusToNull=$con->prepare("UPDATE hosteler_data SET status ='0', outing_type='' where user_id=:var_user_id LIMIT 1");
	$stmtupdateUserStatusToNull->bindParam(':var_user_id', $GLOBALS['args']['hosteler_id']); 


	$stmtupdateUserStatusToAccepted=$con->prepare("UPDATE hosteler_data SET status ='2', outing_type='L' where user_id=:var_hosteler_id LIMIT 1");
	$stmtupdateUserStatusToAccepted->bindParam(':var_user_id', $GLOBALS['args']['hosteler_id']); 


	$stmtDeleteTicket=$con->prepare("DELETE FROM active_leave where user_id=:var_user_id AND pass_id=:var_pass_id LIMIT 1");
	$stmtDeleteTicket->bindParam(':var_user_id', $GLOBALS['args']['hosteler_id']);
	$stmtDeleteTicket->bindParam(':var_pass_id', $GLOBALS['args']['var_pass_id']);

	$stmtAllowLeave=$con->prepare("UPDATE active_leave SET warden_id =:var_warden_id, remark=:var_remark, parent_no=:var_parent_no, status='2' where pass_id=:var_pass_id AND user_id=:var_hosteler_id LIMIT 1");
	$stmtAllowLeave->bindParam(':var_user_id', $GLOBALS['args']['hostel_id']);
	$stmtAllowLeave->bindParam(':var_warden_id', $GLOBALS['args']['user_id']);
	$stmtAllowLeave->bindParam(':var_remark', $GLOBALS['args']['remark']);
	$stmtAllowLeave->bindParam(':var_parent_no', $GLOBALS['args']['parent_no']);
	$stmtAllowLeave->bindParam(':var_pass_id', $GLOBALS['args']['pass_id']);

	$stmtcancelLeave=$con->prepare("DELETE FROM active_leave where user_id=:var_user_id LIMIT 1");
	$stmtcancelLeave->bindParam(':var_user_id', $GLOBALS['args']['user_id']);

	$stmtgetLeaveTicket=$con->prepare("SELECT user_id FROM active_leave where pass_id=:var_pass_id AND status='1' LIMIT 1");
	$stmtgetLeaveTicket->bindParam(':var_pass_id', $GLOBALS['args']['var_pass_id']);

	$stmtUpdateWing=$con->prepare("UPDATE hosteler_data SET wing =:var_wing where user_id=:var_user_id LIMIT 1");
	$stmtUpdateWing->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtUpdateWing->bindParam(':var_wing', $GLOBALS['args']['wing']);


	$stmtCheckStatus=$con->prepare("SELECT status, outing_type FROM hosteler_data where hostel=:var_hostel_id LIMIT 1");
	$stmtCheckStatus->bindParam(':var_hostel_id', $GLOBALS['args']['hostel_id']);


	$stmtCheckToken=$con->prepare("SELECT * FROM tokens_table where user_id=:var_user_id AND access_token=:var_access_token LIMIT 1");
	$stmtCheckToken->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtCheckToken->bindParam(':var_access_token', $GLOBALS['args']['access_token']);

	$stmtgetLeaveTicket=$con->prepare("SELECT * FROM active_leave where user_id=:var_user_id LIMIT 1");
	$stmtgetLeaveTicket->bindParam(':var_user_id', $GLOBALS['args']['user_id']);

	$stmtuniqueInLeave=$con->prepare("SELECT otp FROM active_leave where otp=:var_otp LIMIT 1");
	$stmtuniqueInLeave->bindParam(':var_otp', $otp);

	$stmtuniqueInDayout=$con->prepare("SELECT otp FROM active_dayout where otp=:var_otp LIMIT 1");
	$stmtuniqueInDayout->bindParam(':var_otp', $otp);


	$stmtgetDayoutTicket=$con->prepare("SELECT * FROM active_dayout where user_id=:var_user_id LIMIT 1");
	$stmtgetDayoutTicket->bindParam(':var_user_id', $GLOBALS['args']['user_id']);

	$stmtgetHostelOfWarden=$con->prepare("SELECT hostel FROM warden_data where user_id=:var_user_id LIMIT 1");
	$stmtgetHostelOfWarden->bindParam(':var_user_id', $GLOBALS['args']['user_id']);

	$stmtgetHostelInfo=$con->prepare("SELECT status,outing_type,pass_id FROM hosteler_data where hostel=:var_hostel_id");
	$stmtgetHostelInfo->bindParam(':var_hostel_id', $GLOBALS['args']['hostel_id']);


}catch(PDOException $e){
	echo "Connection Failed".$e;
}



?>