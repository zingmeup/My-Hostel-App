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
	&&isset($_POST['3bcf7a1e46d843e80074879c625e74e4f19147c5b9c1af3d2df41c251d9a4afc'])&&!empty($_POST['3bcf7a1e46d843e80074879c625e74e4f19147c5b9c1af3d2df41c251d9a4afc'])
	&&isset($_POST['5670ee46d843e80074879c625e74e4ab87925190deaf3d2df41c251d9a4afa9'])&&!empty($_POST['5670ee46d843e80074879c625e74e4ab87925190deaf3d2df41c251d9a4afa9'])
	&&isset($_POST['69878d843e80074879c625e74e4ab87925190deaf3d2df41c251d9a4afa9'])&&!empty($_POST['69878d843e80074879c625e74e4ab87925190deaf3d2df41c251d9a4afa9'])) {
		$GLOBALS['args']['user_id']=strtoupper(substr(dataCleaner($_POST['b82c70d9c0d87882bcb6551a8e70da84eca9472e2817ca61ab0ecdc885e6e0fa']), 0, 11));
		$GLOBALS['args']['access_token']=dataCleaner($_POST['3fc10ee46d843e80074879c625e74e4f19147c5b9c1af3d2df41c251d9a4afa9']);
		$GLOBALS['args']['response']=dataCleaner($_POST['3bcf7a1e46d843e80074879c625e74e4f19147c5b9c1af3d2df41c251d9a4afc']);
		$GLOBALS['args']['type']=dataCleaner($_POST['5670ee46d843e80074879c625e74e4ab87925190deaf3d2df41c251d9a4afa9']);
		$GLOBALS['args']['outing_type']=dataCleaner($_POST['69878d843e80074879c625e74e4ab87925190deaf3d2df41c251d9a4afa9']);
		if($GLOBALS['args']['type']=='OTP'){
			$GLOBALS['args']['OTP']=dataCleaner($_POST['3fc10ee46d843e80074879c625e74e4ab87925190deaf3d2df41c251d9a4afa9']);
		}else if($GLOBALS['args']['type']=='UID'){
			$GLOBALS['args']['UID']=dataCleaner($_POST['3fc10ee46d843e80074879c625e74e4ab87925190deaf3d2df41c251d9a4afa9']);
		}else if($GLOBALS['args']['type']=='QR'){
			$GLOBALS['args']['hash']=dataCleaner($_POST['3fc10ee46d843e80074879c625e74e4ab87925190deaf3d2df41c251d9a4afa9']);
		}else{
			return FALSE;
		}
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

function getHashForUid(){
	require $_SERVER['DOCUMENT_ROOT']."/api/security/dbusers/hosteler_image_manager.php";
	$stmtgetTypeOtp->execute();
	$stmtgetTypeOtp->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtgetTypeOtp->fetchAll();
	$con=null;
	if (count($result)>0){
		$GLOBALS['args']['OTP']=$result[0]['otp'];
		$GLOBALS['args']['hash']=md5($GLOBALS['args']['OTP']."cniGEL".$GLOBALS['args']['outing_type']);
		return TRUE;
	}
	return FALSE;
}

function getOutingForHash(){
	require $_SERVER['DOCUMENT_ROOT']."api/security/dbusers/outing_manager_security.php";
	$stmtgetOuting->execute();
	$stmtgetOuting->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtgetOuting->fetchAll();
	if (count($result)>0 && $result[0]['type']==$GLOBALS['args']['outing_type']) {
		if($GLOBALS['args']['response']=='IN'){
			if($result[0]['status']=='3'){
				$GLOBALS['response']['message']="found";
			}else{
				$GLOBALS['response']['message']="mismatch";
			}
		}else if($GLOBALS['args']['response']=='OUT'){
			if($result[0]['status']=='2'){
				$GLOBALS['response']['message']="found";
			}else{
				$GLOBALS['response']['message']="mismatch";
			}
		}else{
			$GLOBALS['response']['message']="qr check";
		}
		$GLOBALS['response']['result']="success";
		$GLOBALS['response']['outing']=$result[0];
		return TRUE;
	}else{
		$GLOBALS['response']['result']="failed";
		$GLOBALS['response']['message']="Outing Not Found";
	}
	return FALSE;
}

if (checkHeaders()) {
	if (checkArguments()) {
		if (checkToken()) {
			if ($GLOBALS['args']['type']=='UID') {
				if(getHashForUid()){
					getOutingForHash();
				}else{
					$GLOBALS['response']['result']="failed";
					$GLOBALS['response']['message']="uid not found";
				}
			}else if($GLOBALS['args']['type']=='OTP'){
				$GLOBALS['args']['hash']=md5($GLOBALS['args']['OTP']."cniGEL".$GLOBALS['args']['outing_type']);
				getOutingForHash();
			}else if($GLOBALS['args']['type']=='QR'){
				getOutingForHash();

			}
			if ($GLOBALS['args']['response']=='IN') {
				// code...
			}else if ($GLOBALS['args']['response']=='OUT') {
				// code...
			}else if ($GLOBALS['args']['response']=='CHECK') {
				// code...
			}else{
				$GLOBALS['response']['result']="failed";
				$GLOBALS['response']['error']=TRUE;
				$GLOBALS['response']['error_code']="IVR";
				$GLOBALS['response']['error_message']="Invalid Response";
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
