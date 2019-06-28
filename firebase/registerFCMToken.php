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
		if ($GLOBALS['headers']['RCPTEQ']=='VTFob1lXRnRhREZaTVd4V1lUQlNWbGRX') {
			return true;
		}
	}
	return false;
}

function checkArguments(){
	$argsMissing=false;
	if (isset($_POST['6551a8e70da8'])&&!empty($_POST['6551a8e70da8'])
	&&isset($_POST['80074879c625'])&&!empty($_POST['80074879c625'])
	&&isset($_POST['6ab3dca9fc12'])&&!empty($_POST['6ab3dca9fc12'])) {
		$GLOBALS['args']['user_id']=strtoupper(substr(dataCleaner($_POST['6551a8e70da8']),0,11));
		$GLOBALS['args']['access_token']=dataCleaner($_POST['80074879c625']);
		$GLOBALS['args']['fcm_token']=dataCleaner($_POST['6ab3dca9fc12']);
	}else{
		$argsMissing=true;
	}
	return !$argsMissing;
}

function checkToken(){
	require $_SERVER['DOCUMENT_ROOT']."/firebase/dbcon/fcmdbcon.php";
	$stmtCheckToken->execute();
	$stmtCheckToken->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtCheckToken->fetchAll();
	$con=null;
	if (count($result)>0) {
		$GLOBALS['database']['tokens_table']=$result[0];
		return TRUE;
	}else{
		return FALSE;
	}
}
function checkFCMTokenExists(){
	require $_SERVER['DOCUMENT_ROOT']."/firebase/dbcon/fcmdbcon.php";
	$stmtCheckFCMToken->execute();
	$stmtCheckFCMToken->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtCheckFCMToken->fetchAll();
	$con=null;
	if (count($result)>0) {
		return TRUE;
	}else{
		return FALSE;
	}
}
function insertFCMToken(){
	require $_SERVER['DOCUMENT_ROOT']."/firebase/dbcon/fcmdbcon.php";
	$stmtinsertFCMToken->execute();
}
function updateFCMToken(){
	require $_SERVER['DOCUMENT_ROOT']."/firebase/dbcon/fcmdbcon.php";
	$stmtupdateFCMToken->execute();
}

if (checkHeaders()) {
	if (checkArguments()) {
		if (checkToken()) {
			if (checkFCMTokenExists()) {
				updateFCMToken();
			}else{
				insertFCMToken();
			}
			$GLOBALS['response']['result']="success";
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
