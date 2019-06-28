<?php
//include 'res/logger.php';
$GLOBALS['timeout']['connection']=15;
$GLOBALS['timeout']['operation']=20;
$GLOBALS['response']['error']=false;
$GLOBALS['response']['error_code']=NULL;
$GLOBALS['response']['error_message']=NULL;
function dataCleaner($dirty){
	$clean=htmlspecialchars(strip_tags(addslashes(trim(filter_var($dirty, FILTER_SANITIZE_STRING)))));
	return $clean;
}

function checkHeaders(){
	$GLOBALS['headers']=getallheaders();
	if (isset($GLOBALS['headers']['RCPTEQ'])&&!empty($GLOBALS['headers']['RCPTEQ'])){
		if ($GLOBALS['headers']['RCPTEQ']=='VjFod1QxVXlSa2hWYTJ4WFltMDRPUT09') {
			return true;
		}
	}
	return false;
}

function checkArguments(){
	$argsMissing=false;
	if (isset($_POST['6551a8e70da8'])&&!empty($_POST['6551a8e70da8'])
	&&isset($_POST['80074879c625'])&&!empty($_POST['80074879c625'])
	&&isset($_POST['b34f346e4c80'])&&!empty($_POST['b34f346e4c80'])
	&&isset($_POST['e2fb1646e999'])&&!empty($_POST['e2fb1646e999'])
	&&isset($_POST['0299368d9519'])&&!empty($_POST['0299368d9519'])
	&&isset($_POST['2b4d521020e3'])&&!empty($_POST['2b4d521020e3'])) {
		$GLOBALS['args']['user_id']=strtoupper(substr(dataCleaner($_POST['6551a8e70da8']),0,11));
		$GLOBALS['args']['access_token']=dataCleaner($_POST['80074879c625']);
		$GLOBALS['args']['place']=dataCleaner($_POST['b34f346e4c80']);
		$GLOBALS['args']['purpose']=dataCleaner($_POST['e2fb1646e999']);
		$GLOBALS['args']['going_with']=dataCleaner($_POST['0299368d9519']);
		$GLOBALS['args']['phone']=dataCleaner($_POST['2b4d521020e3']);
	}else{
		$argsMissing=true;
	}
	return !$argsMissing;
}

function checkToken(){
	require $_SERVER['DOCUMENT_ROOT']."/api/dbusers/token_manager_con.php";
	$stmtCheckToken->execute();
	$stmtCheckToken->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtCheckToken->fetchAll();
	$con=null;
	if (count($result)>0 && $result[0]['block']==0 &&  $result[0]['user_type']=='S') {
		$GLOBALS['database']['tokens_table']=$result[0];
		return TRUE;
	}else{
		return FALSE;
	}
}


function checkStatus(){
	require $_SERVER['DOCUMENT_ROOT']."/api/hosteler/dbusers/outing_manager_hosteler.php";
	$stmtCheckStatus->execute();
	$stmtCheckStatus->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtCheckStatus->fetchAll();
	$con=null;
	if (count($result)>0) {
		$GLOBALS['checkStatus']=$result[0];
		if ($result[0]['outing_type']==NULL) {
			return TRUE;
		}
	}
	return FALSE;
}
function timinigsValid(){
	$timings=json_decode(file_get_Contents($_SERVER['DOCUMENT_ROOT']."api/timings.json"),true);
	$holiday=file_get_Contents($_SERVER['DOCUMENT_ROOT']."api/holiday.txt");
	//var_dump($holiday);
	$gender=$GLOBALS['checkStatus']['gender'];
	if ($holiday==TRUE) {
		$date=time("H:i:s");
		$date=date("H:i:s", $date);
		if($timings['holiday'][$gender]['start']<$date && $date< $timings['holiday'][$gender]['end']){
			return TRUE;
		}else{
			return FALSE;
		}
	}else{
		$dayOfWeek= date('N');
		if ($dayOfWeek>0 && $dayOfWeek<7) {
			$date=time("H:i:s");
			$date=date("H:i:s", $date);
			if($timings['working'][$gender]['start']<$date && $date< $timings['working'][$gender]['end']){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			$date=time("H:i:s");
			$date=date("H:i:s", $date);
			if($timings['holiday'][$gender]['start']<$date && $date< $timings['holiday'][$gender]['end']){
				return TRUE;
			}else{
				return FALSE;
			}
		}
	}
}

function makeOutingRequest(){
	$dt_apply=date('Y-m-d h:i:s');
	$generateOTP=generateOTP('D');
	$GLOBALS['args']['parent_phone']=NULL;
	$GLOBALS['args']['dt_exp_out']=NULL;
	$GLOBALS['args']['d_exp_in']=NULL;
	$GLOBALS['args']['hash']=$generateOTP['hash'];
	$status=2;
	require $_SERVER['DOCUMENT_ROOT']."/api/hosteler/dbusers/outing_manager_hosteler.php";
	if($stmtmakeOutingRequest->execute()){
		if($stmtupdateUserStatusToAccepted->execute()){
			$GLOBALS['response']['request']="success";
			return TRUE;
		}
	}else{
		$con=null;
		return FALSE;
	}
}

function generateOTP($type){
	$outing_type=$type;
	$otp=rand(100000,999999);
	$hash=md5($otp."cniGEL".$outing_type);
	while ((foundInOuting($hash))) {
		$otp=rand(100000,999999);
		$hash=md5($otp."cniGEL".$outing_type);
	}
	$outingArgs['otp']=$otp;
	$outingArgs['hash']=$hash;
	$outingArgs['outing_type']=$outing_type;
	return $outingArgs;
}

function foundInOuting($hash){
	require $_SERVER['DOCUMENT_ROOT']."/api/hosteler/dbusers/outing_manager_hosteler.php";
	$stmtuniqueHashInOuting->execute();
	$stmtuniqueHashInOuting->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtuniqueHashInOuting->fetchAll();
	$con=null;
	if (count($result)>0) {
		return TRUE;
	}else{
		return FALSE;
	}
}
function getUserOuting(){
	require $_SERVER['DOCUMENT_ROOT']."/api/hosteler/dbusers/outing_manager_hosteler.php";
	$stmtgetOuting->execute();
	$stmtgetOuting->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtgetOuting->fetchAll();
	$con=null;
	if (count($result)>0) {
		$GLOBALS['response']['outing_details']=$result[0];
		return TRUE;
	}else{
		return FALSE;
	}
}

if (checkHeaders()) {
	if (checkArguments()) {
		if (checkToken()) {
			if (checkStatus()) {
				if(timinigsValid()){
					if (makeOutingRequest()) {
						getUserOuting();
					}
				}else{
					$GLOBALS['response']['error']=true;
					$GLOBALS['response']['error_code']="IVT";
					$GLOBALS['response']['error_message']="You can not apply at this time.\nPlease refer to the timings table.";
				}
			}else{
				$GLOBALS['response']['error']=true;
				$GLOBALS['response']['error_code']="IVS";
				$GLOBALS['response']['error_message']="You can not apply with this status.\nYou may already have an outing.";
			}

		}else{
			$GLOBALS['response']['error']=TRUE;
			$GLOBALS['response']['error_code']="IVAT";
			$GLOBALS['response']['error_message']="Your services seems to be blocked.\nTry login again.";
		}
	}else{
		$GLOBALS['response']['error']=TRUE;
		$GLOBALS['response']['error_code']="IVA";
		$GLOBALS['response']['error_message']="Invalid Arguments";
	}
}else{
	$GLOBALS['response']['error']=TRUE;
	$GLOBALS['response']['error_code']="IVH";
	$GLOBALS['response']['error_message']="Invalid Headers";
}


echo json_encode($GLOBALS['response']);
