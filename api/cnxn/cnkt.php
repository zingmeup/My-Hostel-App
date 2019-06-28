<?php
$servername="localhost";
$username="root";
$password="";
$dbname="myhostelapp";

$con=NULL;
try{
	$con=new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	$con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

	$stmtCheckUseridExists=$con->prepare("SELECT user_id FROM hosteler_data where user_id=:var_user_id LIMIT 1");
	$stmtCheckUseridExists->bindParam(':var_user_id', $GLOBALS['args']['user_id']);

	$stmtCheckHostelExists=$con->prepare("SELECT id FROM hostels where name=:var_name LIMIT 1");
	$stmtCheckHostelExists->bindParam(':var_name', $hostelName);

	$stmtGetHostelById=$con->prepare("SELECT * FROM hostels where name=:var_name LIMIT 1");
	$stmtGetHostelById->bindParam(':var_name', $hostel);

	$stmtAddHostel=$con->prepare("INSERT INTO hostels values('', :var_type,:var_name, '10')");
	$stmtAddHostel->bindParam(':var_type', $type);
	$stmtAddHostel->bindParam(':var_name', $hostelName);

	$stmtCheckTokenExists=$con->prepare("SELECT * FROM tokens_table where user_id=:var_user_id LIMIT 1");
	$stmtCheckTokenExists->bindParam(':var_user_id', $GLOBALS['args']['user_id']);

	$stmtUpdateToken=$con->prepare("UPDATE tokens_table SET access_token =:var_access_token where user_id=:var_user_id LIMIT 1");
	$stmtUpdateToken->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtUpdateToken->bindParam(':var_access_token', $GLOBALS['result']['access_token']);


	$stmtInsertToken=$con->prepare("INSERT INTO tokens_table values(:var_user_id,:var_access_token)");
	$stmtInsertToken->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtInsertToken->bindParam(':var_access_token', $GLOBALS['result']['access_token']);

	$stmtCheckToken=$con->prepare("SELECT * FROM tokens_table where user_id=:var_user_id AND access_token=:var_access_token LIMIT 1");
	$stmtCheckToken->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtCheckToken->bindParam(':var_access_token', $GLOBALS['args']['access_token']);


	$stmtUpdateBasics=$con->prepare("UPDATE hosteler_data SET user_id =:var_user_id, gender =:var_gender, branch =:var_branch, course =:var_course, section =:var_section,  name =:var_name, hostel =:var_hostel, room =:var_room where user_id=:var_user_id LIMIT 1");
	$stmtUpdateBasics->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtUpdateBasics->bindParam(':var_gender', $GLOBALS['result']['user_info']['gender']);
	$stmtUpdateBasics->bindParam(':var_branch', $GLOBALS['result']['user_info']['branch']);
	$stmtUpdateBasics->bindParam(':var_course', $GLOBALS['result']['user_info']['course']);
	$stmtUpdateBasics->bindParam(':var_section', $GLOBALS['result']['user_info']['section']);
	$stmtUpdateBasics->bindParam(':var_name', $GLOBALS['result']['user_info']['name']);
	$stmtUpdateBasics->bindParam(':var_hostel', $GLOBALS['result']['user_info']['hostel_id']);
	$stmtUpdateBasics->bindParam(':var_room', $GLOBALS['result']['user_info']['room']);

	$stmtUpdatePersonal=$con->prepare("UPDATE hosteler_data SET phone =:var_phone, email =:var_email, img =:var_img where user_id=:var_user_id LIMIT 1");
	$stmtUpdatePersonal->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtUpdatePersonal->bindParam(':var_phone', $GLOBALS['result']['user_info']['phone']);
	$stmtUpdatePersonal->bindParam(':var_email', $GLOBALS['result']['user_info']['email']);
	$stmtUpdatePersonal->bindParam(':var_img', $GLOBALS['result']['user_info']['img']);


	$stmtInsertBasics=$con->prepare("INSERT INTO hosteler_data VALUES( :var_user_id, :var_gender, :var_name, :var_branch, :var_course, :var_section, :var_hostel, :var_room, '', '', '', '', 0, '', 0)");
	$stmtInsertBasics->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtInsertBasics->bindParam(':var_gender', $GLOBALS['result']['user_info']['gender']);
	$stmtInsertBasics->bindParam(':var_branch', $GLOBALS['result']['user_info']['branch']);
	$stmtInsertBasics->bindParam(':var_course', $GLOBALS['result']['user_info']['course']);
	$stmtInsertBasics->bindParam(':var_section', $GLOBALS['result']['user_info']['section']);
	$stmtInsertBasics->bindParam(':var_name', $GLOBALS['result']['user_info']['name']);
	$stmtInsertBasics->bindParam(':var_hostel', $GLOBALS['result']['user_info']['hostel_id']);
	$stmtInsertBasics->bindParam(':var_room', $GLOBALS['result']['user_info']['room']);


	$stmtUpdateWing=$con->prepare("UPDATE hosteler_data SET wing =:var_wing where user_id=:var_user_id LIMIT 1");
	$stmtUpdateWing->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtUpdateWing->bindParam(':var_wing', $GLOBALS['args']['wing']);


	$stmtCheckStatus=$con->prepare("SELECT status, outing_type FROM hosteler_data where user_id=:var_user_id LIMIT 1");
	$stmtCheckStatus->bindParam(':var_user_id', $GLOBALS['args']['user_id']);



	$stmtgetLeaveTicket=$con->prepare("SELECT * FROM active_leave where user_id=:var_user_id LIMIT 1");
	$stmtgetLeaveTicket->bindParam(':var_user_id', $GLOBALS['args']['user_id']);

	$stmtuniqueInLeave=$con->prepare("SELECT otp FROM active_leave where otp=:var_otp LIMIT 1");
	$stmtuniqueInLeave->bindParam(':var_otp', $otp);

	$stmtuniqueInDayout=$con->prepare("SELECT otp FROM active_dayout where otp=:var_otp LIMIT 1");
	$stmtuniqueInDayout->bindParam(':var_otp', $otp);


	$stmtgetDayoutTicket=$con->prepare("SELECT * FROM active_dayout where user_id=:var_user_id LIMIT 1");
	$stmtgetDayoutTicket->bindParam(':var_user_id', $GLOBALS['args']['user_id']);

	$stmtmakeDayoutRequest=$con->prepare("INSERT INTO active_dayout VALUES( '', :var_place, :var_purpose, :var_going_with, :var_hostel, :var_date_of_apply, :var_time_of_apply, '', '', '', '', '', '', '2', :var_user_id, :var_otp, :var_hash, :var_phone)");
	$stmtmakeDayoutRequest->bindParam(':var_place', $GLOBALS['args']['place']);
	$stmtmakeDayoutRequest->bindParam(':var_purpose', $GLOBALS['args']['purpose']);
	$stmtmakeDayoutRequest->bindParam(':var_going_with', $GLOBALS['args']['going_with']);
	$stmtmakeDayoutRequest->bindParam(':var_date_of_apply', $date_of_apply);
	$stmtmakeDayoutRequest->bindParam(':var_time_of_apply', $time_of_apply);
	$stmtmakeDayoutRequest->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtmakeDayoutRequest->bindParam(':var_phone', $GLOBALS['args']['phone']);
	$stmtmakeLeaveRequest->bindParam(':var_hostel', $GLOBALS['args']['hostel']); 
	$stmtmakeDayoutRequest->bindParam(':var_otp', $otp);
	$stmtmakeDayoutRequest->bindParam(':var_hash', $hash);

	$stmtupdateUserStatusToRequested=$con->prepare("UPDATE hosteler_data SET status ='1', outing_type=:var_outing_type where user_id=:var_user_id LIMIT 1");
	$stmtupdateUserStatusToRequested->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtupdateUserStatusToRequested->bindParam(':var_outing_type', $outing_type);

	$stmtupdateUserStatusToAccepted=$con->prepare("UPDATE hosteler_data SET status ='2', outing_type=:var_outing_type where user_id=:var_user_id LIMIT 1");
	$stmtupdateUserStatusToAccepted->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtupdateUserStatusToAccepted->bindParam(':var_outing_type', $outing_type);

	$stmtmakeLeaveRequest=$con->prepare("INSERT INTO active_leave VALUES( '', :var_user_id, :var_otp, :var_hash, :var_phone, :var_place, :var_purpose, :var_going_with, :var_hostel, :var_date_of_apply, :var_time_of_apply, :var_date_exp_out, :var_date_exp_in, :var_time_exp_out, :var_parent_no, '', '', '', '', '', '','','', '1')");
	$stmtmakeLeaveRequest->bindParam(':var_place', $GLOBALS['args']['place']);
	$stmtmakeLeaveRequest->bindParam(':var_purpose', $GLOBALS['args']['purpose']);
	$stmtmakeLeaveRequest->bindParam(':var_going_with', $GLOBALS['args']['going_with']);
	$stmtmakeLeaveRequest->bindParam(':var_date_exp_out', $GLOBALS['args']['date_exp_out']);
	$stmtmakeLeaveRequest->bindParam(':var_date_exp_in', $GLOBALS['args']['date_exp_in']);
	$stmtmakeLeaveRequest->bindParam(':var_time_exp_out', $GLOBALS['args']['time_exp_out']);
	$stmtmakeLeaveRequest->bindParam(':var_date_of_apply', $date_of_apply);
	$stmtmakeLeaveRequest->bindParam(':var_time_of_apply', $time_of_apply);
	$stmtmakeLeaveRequest->bindParam(':var_user_id', $GLOBALS['args']['user_id']);
	$stmtmakeLeaveRequest->bindParam(':var_phone', $GLOBALS['args']['phone']);
	$stmtmakeLeaveRequest->bindParam(':var_hostel', $GLOBALS['args']['hostel']);
	$stmtmakeLeaveRequest->bindParam(':var_parent_no', $GLOBALS['args']['parent_no']); 
	$stmtmakeLeaveRequest->bindParam(':var_otp', $otp);
	$stmtmakeLeaveRequest->bindParam(':var_hash', $hash);



	$stmtIsCSY=$con->prepare("SELECT * from cute WHERE course_code=:var_course_code AND year=:var_year AND section=:var_section LIMIT 1");
	$stmtIsCSY->bindParam(':var_course_code', $course_code);
	$stmtIsCSY->bindParam(':var_year', $year);
	$stmtIsCSY->bindParam(':var_section', $section);


	$stmtInsertCSY=$con->prepare("INSERT INTO cute VALUES('', :var_course_code,:var_year,:var_section,:var_pass,'','','',:var_token,0)");
	$stmtInsertCSY->bindParam(':var_course_code', $course_code);
	$stmtInsertCSY->bindParam(':var_year', $year);
	$stmtInsertCSY->bindParam(':var_section', $section);
	$stmtInsertCSY->bindParam(':var_pass', $pass);
	$stmtInsertCSY->bindParam(':var_token', $token);


	$stmtIsTokenValid=$con->prepare("SELECT * from cute WHERE course_code=:var_course_code AND year=:var_year AND section=:var_section AND token=:var_token LIMIT 1");
	$stmtIsTokenValid->bindParam(':var_course_code', $course_code);
	$stmtIsTokenValid->bindParam(':var_year', $year);
	$stmtIsTokenValid->bindParam(':var_section', $section);
	$stmtIsTokenValid->bindParam(':var_token', $token);


	$stmtUpload=$con->prepare("UPDATE cute set device_id=:var_device_id, imei=:var_imei, image=:var_image,report=0 WHERE course_code=:var_course_code AND year=:var_year AND section=:var_section AND token=:var_token");
	$stmtUpload->bindParam(':var_course_code', $course_code);
	$stmtUpload->bindParam(':var_year', $year);
	$stmtUpload->bindParam(':var_section', $section);
	$stmtUpload->bindParam(':var_token', $token);
	$stmtUpload->bindParam(':var_device_id', $device_id);
	$stmtUpload->bindParam(':var_imei', $imei);
	$stmtUpload->bindParam(':var_image', $image);


	$stmtFetchAllFor=$con->prepare("SELECT id,course_code,year,section,report from cute WHERE course_code=:var_course_code AND year=:var_year");
	$stmtFetchAllFor->bindParam(':var_course_code', $course_code);
	$stmtFetchAllFor->bindParam(':var_year', $year);


	$stmtfetchTimetable=$con->prepare("SELECT image from cute WHERE id=:var_id LIMIT 1");
	$stmtfetchTimetable->bindParam(':var_id', $id);

	$stmtGetreportCount=$con->prepare("SELECT report, device_id, imei FROM cute WHERE id=:var_id LIMIT 1");
	$stmtGetreportCount->bindParam(':var_id', $id);

	$stmtPutreportCount=$con->prepare("UPDATE cute SET report=:var_report WHERE id=:var_id LIMIT 1");
	$stmtPutreportCount->bindParam(':var_id', $id);
	$stmtPutreportCount->bindParam(':var_report', $report);

	$stmtAddToBlock=$con->prepare("INSERT INTO blocklist VALUES('', :var_device_id, :var_imei)");
	$stmtAddToBlock->bindParam(':var_device_id', $device_id);
	$stmtAddToBlock->bindParam(':var_imei', $imei);

	$stmtDelete=$con->prepare("DELETE FROM cute WHERE id=:var_id LIMIT 1");
	$stmtDelete->bindParam(':var_id', $id);


}catch(PDOException $e){
	echo "Connection Failed".$e;
}



?>