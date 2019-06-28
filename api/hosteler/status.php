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
		if ($GLOBALS['headers']['RCPTEQ']=='YWRlNTA2NWIyNGRlNTlmOWRhN2IzNDRz') {
			return true;
		}
	}
	return false;
}
function checkArguments(){
	$argsMissing=false;
	if (isset($_POST['6551a8e70da8'])&&!empty($_POST['6551a8e70da8'])
	&&isset($_POST['80074879c625'])&&!empty($_POST['80074879c625'])) {
		$GLOBALS['args']['user_id']=strtoupper(substr(dataCleaner($_POST['6551a8e70da8']), 0, 11));
		$GLOBALS['args']['access_token']=dataCleaner($_POST['80074879c625']);
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
	require $_SERVER['DOCUMENT_ROOT']."/api/hosteler/dbusers/user_data_hosteler.php";
	$stmtCheckStatus->execute();
	$stmtCheckStatus->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtCheckStatus->fetchAll();
	$con=null;
	if (count($result)>0) {
		$GLOBALS['database']['user_data']=$result[0];
		$GLOBALS['response']['outing_type']=$result[0]['outing_type'];
		$GLOBALS['args']['hash']=md5($result[0]['otp']."cniGEL".$result[0]['outing_type']);
		if ($result[0]['outing_type']!=NULL) {
			return getOuting();
		}
	}
	return FALSE;
}

function getOuting(){
	require $_SERVER['DOCUMENT_ROOT']."/api/hosteler/dbusers/outing_manager_hosteler.php";
	$stmtgetOuting->execute();
	$stmtgetOuting->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtgetOuting->fetchAll();
	$con=null;
	if (count($result)>0) {
		$GLOBALS['database']['active_outing']=$result[0];
		return TRUE;
	}else{
		return FALSE;
	}
}
function getAdvertisements(){
	require $_SERVER['DOCUMENT_ROOT']."/api/hosteler/dbusers/ads_manager_hosteler.php";
	$stmtgetActiveAds->execute();
	$stmtgetActiveAds->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtgetActiveAds->fetchAll();
	$con=null;
	if (count($result)>0) {
		$GLOBALS['database']['advertisements']=$result;
		return TRUE;
	}else{
		return FALSE;
	}
}

function getTimings($gender){
	$timings=json_decode(file_get_Contents($_SERVER['DOCUMENT_ROOT']."api/timings.json"),true);
	$holiday=file_get_Contents($_SERVER['DOCUMENT_ROOT']."api/holiday.txt");
	if ($holiday==TRUE) {
		return $timings['holiday'][$gender];
	}else{
		$dayOfWeek= date('N');
		if ($dayOfWeek>0 && $dayOfWeek<7) {
				return $timings['working'][$gender];
		}else{
				return $timings['holiday'][$gender];
		}
	}
}

if (checkHeaders()) {
	if (checkArguments()) {
		if (checkToken()) {
			if(checkStatus()){
				$GLOBALS['response']['active_outing']=$GLOBALS['database']['active_outing'];
			}
			getAdvertisements();
			$GLOBALS['response']['advertisements']=$GLOBALS['database']['advertisements'];
			$GLOBALS['response']['user_data']=$GLOBALS['database']['user_data'];
			$GLOBALS['response']['timings']=getTimings($GLOBALS['database']['user_data']['gender']);
		}else{
			$GLOBALS['response']['error']=TRUE;
			$GLOBALS['response']['error_code']="IVAT";
			$GLOBALS['response']['error_message']="Your services seems to be blocked.\nTry login again.";
		}
	}else{
		$GLOBALS['response']['error']=true;
		$GLOBALS['response']['errorCode']="IA";
		$GLOBALS['response']['errorMessage']="Invalid Argument types";
	}
}else{

	$GLOBALS['response']['error']=true;
	$GLOBALS['response']['errorCode']="IH";
	$GLOBALS['response']['errorMessage']="Invalid Headers";
}


echo json_encode($GLOBALS['response']);
