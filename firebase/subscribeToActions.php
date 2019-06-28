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
	&&isset($_POST['6ab3dca9fc12'])&&!empty($_POST['6ab3dca9fc12'])
	&&isset($_POST['786cbfaedf9e'])&&!empty($_POST['786cbfaedf9e'])) {
		$GLOBALS['args']['user_id']=strtoupper(substr(dataCleaner($_POST['6551a8e70da8']),0,11));
		$GLOBALS['args']['access_token']=dataCleaner($_POST['80074879c625']);
		$GLOBALS['args']['fcm_token']=dataCleaner($_POST['6ab3dca9fc12']);
		$res=dataCleaner($_POST['786cbfaedf9e']);
		if ($res=="set") {
			$GLOBALS['args']['service_action']=1;
		}else{
			$GLOBALS['args']['service_action']=0;
		}
	}else{
		//echo json_encode($_POST);
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
	$stmtupdateServiceAction->execute();
}
function updateFCMToken(){
	require $_SERVER['DOCUMENT_ROOT']."/firebase/dbcon/fcmdbcon.php";
	$stmtupdateFCMToken->execute();
	$stmtupdateServiceAction->execute();
}

if (checkHeaders()) {
	if (checkArguments()) {
		if (checkToken()) {
			if (checkFCMTokenExists()) {
				updateFCMToken();
			}else{
				insertFCMToken();
			}
			if ($GLOBALS['args']['service_action']==1) {
				$GLOBALS['response']['result']="Subscribed to Actions";
			}else{
				$GLOBALS['response']['result']="Unsubscribed to Actions";	
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
