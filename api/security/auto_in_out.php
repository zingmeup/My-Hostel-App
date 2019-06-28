<?php
$GLOBALS['response']['error']=false;
$GLOBALS['response']['error_code']=NULL;
$GLOBALS['response']['error_message']=NULL;
function dataCleaner($dirty){
	$clean=htmlspecialchars(strip_tags(addslashes(trim(filter_var($dirty, FILTER_SANITIZE_STRING)))));
	return $clean;
}

function checkHeaders(){
	$GLOBALS['headers']=getallheaders();
	if (isset($GLOBALS['headers']['X-Csrf-Token'])&&!empty($GLOBALS['headers']['X-Csrf-Token'])&&isset($GLOBALS['headers']['RCPTEQ'])&&!empty($GLOBALS['headers']['RCPTEQ'])){
		if ($GLOBALS['headers']['X-Csrf-Token']=='UFgzS244RFlBbno0VjMyT1pjU285RkMwSDluMGdVcmQ4aHVtMzh4SXpCZk5QYUNGbUIxNmhGUGhjUTgyNjMyMUY0OVZqZytNL2pUSXNsb25VMkIzUTNaNjc2K0o4QWIxZjhucHkrUVJhc3RYS0pYTDlEUC9PbkU3SGZuQ1hId1MwVUFLclhNRGVjVzduOEJBVE84RXpNYkkvY2hCeHBmblhBdlBpZC9sYi9ObjJqZVIzeUNaOG9xSzVOdHFvZk84VlYrWXhFVVRSNjQxbXZxZGJOUVFSMFYwWFVzRXMxcG4rSVB6NDdDOU5nSENKU1pvTC9mZGpkWE5id3UxdHo5ag='
		&&$GLOBALS['headers']['RCPTEQ']=='bUIxadhknaduUITyNjMyMUY0') {
			return true;
		}
	}
	return false;
}
function checkArguments(){
	$argsMissing=false;
	if (isset($_POST['b82c70d9c0d87882bcb6551a8e70da84eca9472e2817ca61ab0ecdc885e6e0fa'])&&!empty($_POST['b82c70d9c0d87882bcb6551a8e70da84eca9472e2817ca61ab0ecdc885e6e0fa'])
	&&isset($_POST['3fc10ee46d843e80074879c625e74e4f19147c5b9c1af3d2df41c251d9a4afa9'])&&!empty($_POST['3fc10ee46d843e80074879c625e74e4f19147c5b9c1af3d2df41c251d9a4afa9'])
	&&isset($_POST['3fc10ee46d843e80074879c625e74e4ab87925190deaf3d2df41c251d9a4afa9'])&&!empty($_POST['3fc10ee46d843e80074879c625e74e4ab87925190deaf3d2df41c251d9a4afa9'])
	&&isset($_POST['3bcf7a1e46d843e80074879c625e74e4f19147c5b9c1af3d2df41c251d9a4afc'])&&!empty($_POST['3bcf7a1e46d843e80074879c625e74e4f19147c5b9c1af3d2df41c251d9a4afc'])) {
		$GLOBALS['args']['user_id']=strtoupper(substr(dataCleaner($_POST['b82c70d9c0d87882bcb6551a8e70da84eca9472e2817ca61ab0ecdc885e6e0fa']), 0, 11));
		$GLOBALS['args']['access_token']=dataCleaner($_POST['3fc10ee46d843e80074879c625e74e4f19147c5b9c1af3d2df41c251d9a4afa9']);
		$GLOBALS['args']['hash']=dataCleaner($_POST['3fc10ee46d843e80074879c625e74e4ab87925190deaf3d2df41c251d9a4afa9']);
		$GLOBALS['args']['response']=dataCleaner($_POST['3bcf7a1e46d843e80074879c625e74e4f19147c5b9c1af3d2df41c251d9a4afc']);
	}else{
		$argsMissing=true;
	}
	return !$argsMissing;
}

function checkToken(){
	require $_SERVER['DOCUMENT_ROOT']."/api/security/dbusers/token_manager_con.php";
	$stmtCheckToken->execute();
	$stmtCheckToken->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtCheckToken->fetchAll();
	$con=null;
	if (count($result)>0 && $result[0]['block']==0) {
		$GLOBALS['database']['tokens_table']=$result[0];
		return TRUE;
	}else{
		return FALSE;
	}
}


function respondTORequest(){
	require $_SERVER['DOCUMENT_ROOT']."api/security/dbusers/outing_manager_security.php";
	$GLOBALS['LATE']=NULL;
	$stmtgetOuting->execute();
	$stmtgetOuting->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtgetOuting->fetchAll();
	if (count($result)>0) {
		if($GLOBALS['args']['response']=='OUT' && $result[0]['status']=='2'){
			$result[0]['sign_sec_out']=$GLOBALS['args']['user_id'];
			if(respondTOEntryOut($result[0])){
				return TRUE;
			}
		}else if($GLOBALS['args']['response']=='IN' && $result[0]['status']=='3'){
			$result[0]['sign_sec_in']=$GLOBALS['args']['user_id'];
			$result[0]['dt_in']=date("Y-m-d H:i:s");
			if ($result[0]['type']=='L') {
				if(respondToEntryInLeave($result[0])){
					return TRUE;
				}else{
					$GLOBALS['response']['message']="respondToEntryInLeave failed";
				}
			}else if($result[0]['type']=='D'){
				if(respondTOEntryInDayout($result[0])){
					return TRUE;
				}else{
					$GLOBALS['response']['message']="respondTOEntryInDayout failed";
				}
			}else{
				$GLOBALS['response']['message']="neither D or L";
			}
		}else{
			$GLOBALS['response']['message']="Request didn't match";
		}
	}else{
		$GLOBALS['response']['message']="No such Record found";
	}
	return FALSE;
}

function todaysLastTimeIn($gender){
	$timings=json_decode(file_get_Contents($_SERVER['DOCUMENT_ROOT']."api/timings.json"),true);
	$holiday=file_get_Contents($_SERVER['DOCUMENT_ROOT']."api/holiday.txt");
	if ($holiday==TRUE) {
		return strtotime($timings['holiday'][$gender]['last']);
	}else{
		$dayOfWeek= date('N');
		if ($dayOfWeek>0 && $dayOfWeek<7) {
			return strtotime($timings['working'][$gender]['last']);
		}else{
			return strtotime($timings['holiday'][$gender]['last']);
		}
	}
}

function respondTOEntryOut($Outing){
	$GLOBALS['args']['dt_out']=date("Y-m-d H:i:s");
	$Outing['dt_out']=$GLOBALS['args']['dt_out'];
	require $_SERVER['DOCUMENT_ROOT']."api/security/dbusers/outing_manager_security.php";
	$stmtsetOutingOut->execute();
	if($stmtsetOutingOut->rowcount()>0){
		$stmtsetUserOut->execute();
		if($stmtsetUserOut->rowcount()>0){
			$Outing['status']='3';
			$GLOBALS['response']['outing']=$Outing;
			return TRUE;
		}else{
			$GLOBALS['response']['message']="Setting User failed";
		}
	}else{
		$GLOBALS['response']['message']="Setting OUting failed";
	}
}

function respondToEntryInLeave($Outing){
	$diff=strtotime(date("Y-m-d"))-strtotime($Outing['d_exp_in']);
	$days = floor($diff/86400);
	if($days>0){
		//echo "Leave Day difference: >0 ".$days;
		$Outing['bonus']*=((100-($days*10))/100);
		$GLOBALS['LATE']="0000-00-".floor($days)." 00:00:00";
	}else{
		//echo "Leave Day difference: < 0 on time ".$days;
		$Outing['bonus']+=15;
	}

	require $_SERVER['DOCUMENT_ROOT']."api/security/dbusers/outing_manager_security.php";
	$stmtsetOutingIn->execute();
	if($stmtsetOutingIn->rowcount()>0){
		$stmtsetUserIn->execute();
		if($stmtsetUserIn->rowcount()>0){
			//	var_dump($Outing);
			$stmtmoveToPassive->execute();
			if($stmtmoveToPassive->rowcount()>0){
				$stmtdeleteOuting->execute();
					$Outing['status']='4';
				$GLOBALS['response']['outing']=$Outing;
				return TRUE;
			}else{
				$GLOBALS['response']['message']="row count zero";
				return FALSE;
			}
		}
	}
}

function respondTOEntryInDayout($Outing){
	//echo "<br>"."DAYOUT";
	$diff=strtotime(date("Y-m-d"))-strtotime($Outing['dt_out']);
	$days = floor($diff/86400)+1;
	//echo "<br>"."DAYS".$days;
	if ($days==0) {
		//echo "<br>"."Dayout Day difference: 0";
		$todaysLastTimeIn=todaysLastTimeIn($Outing['gender']);
		$diff=strtotime($todaysLastTimeIn)-strtotime(date("H:i:s"));
		if ($diff>0) {
			//echo "<br>"."Time more than last datout> 0".$diff;
			//echo "<br>"."current bonus: ".$Outing['bonus'];
			$Outing['bonus']-=$diff;
			$GLOBALS['LATE']="0000-00-00 ".gmdate('H:i:s', $diff);
			//echo "<br>"."New bonus: ".$Outing['bonus'];
		}else{
			//echo "<br>"."ON TIME last datout> 0";
			//echo "<br>"."current bonus: ".$Outing['bonus'];
			$Outing['bonus']+=15;
			//echo "<br>"."New bonus: ".$Outing['bonus'];
		}
	}else{
		//echo "Dayout Day difference not 0".$days;
		$Outing['bonus']=0;
		//echo $diff;
		$GLOBALS['LATE']="0000-00-".floor($days)." 00:00:00";
		//echo $GLOBALS['LATE'];
	}
	require $_SERVER['DOCUMENT_ROOT']."api/security/dbusers/outing_manager_security.php";
	$stmtsetOutingIn->execute();
	if($stmtsetOutingIn->rowcount()>0){
		$stmtsetUserIn->execute();
		if($stmtsetUserIn->rowcount()>0){
			//	var_dump($Outing);
			$stmtmoveToPassive->execute();
			if($stmtmoveToPassive->rowcount()>0){
				$stmtdeleteOuting->execute();
					$Outing['status']='4';
				$GLOBALS['response']['outing']=$Outing;
				return TRUE;
			}else{
				$GLOBALS['response']['message']="row count zero";
				return FALSE;
			}
		}
	}
}

if (checkHeaders()) {
	if (checkArguments()) {
		if (checkToken()) {
			if(respondTORequest()){
				$GLOBALS['response']['result']="success";
			}else{
				$GLOBALS['response']['result']="failed";

			}
		}else{
			$GLOBALS['response']['error']=TRUE;
			$GLOBALS['response']['error_code']="IVAT";
			$GLOBALS['response']['error_message']="Invalid Token";
		}
	}else{
		$GLOBALS['response']['error']=true;
		$GLOBALS['response']['error_code']="IA";
		$GLOBALS['response']['error_message']="Invalid Argument types";
	}
}else{

	$GLOBALS['response']['error']=true;
	$GLOBALS['response']['error_code']="IH";
	$GLOBALS['response']['error_message']="Invalid Headers";
}


echo json_encode($GLOBALS['response']);
