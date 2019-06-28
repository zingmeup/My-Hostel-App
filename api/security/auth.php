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
	if (isset($GLOBALS['headers']['X-Csrf-Token'])&&!empty($GLOBALS['headers']['X-Csrf-Token'])&&isset($GLOBALS['headers']['RCPTEQ'])&&!empty($GLOBALS['headers']['RCPTEQ'])){
		if ($GLOBALS['headers']['X-Csrf-Token']=='UFgzS244RFlBbno0VjMyT1pjU285RkMwSDluMGdVcmQ4aHVtMzh4SXpCZk5QYUNGbUIxNmhGUGhjUTgyNjMyMUY0OVZqZytNL2pUSXNsb25VMkIzUTNaNjc2K0o4QWIxZjhucHkrUVJhc3RYS0pYTDlEUC9PbkU3SGZuQ1hId1MwVUFLclhNRGVjVzduOEJBVE84RXpNYkkvY2hCeHBmblhBdlBpZC9sYi9ObjJqZVIzeUNaOG9xSzVOdHFvZk84VlYrWXhFVVRSNjQxbXZxZGJOUVFSMFYwWFVzRXMxcG4rSVB6NDdDOU5nSENKU1pvTC9mZGpkWE5id3UxdHo5ag='
		&&$GLOBALS['headers']['RCPTEQ']=='bUI768UsecUTgyNjMyMUY0') {
			return true;
		}
	}
	return false;
}

function checkArguments(){
	$argsMissing=false;
	if (isset($_POST['b82c70d9c0d87882bcb6551a8e70da84eca9472e2817ca61ab0ecdc885e6e0fa'])&&!empty($_POST['b82c70d9c0d87882bcb6551a8e70da84eca9472e2817ca61ab0ecdc885e6e0fa'])
	&&isset($_POST['3fc10ee46d843e80074879c625e74e4f19147c5b9c1af3d2df41c251d9a4afa9'])&&!empty($_POST['3fc10ee46d843e80074879c625e74e4f19147c5b9c1af3d2df41c251d9a4afa9'])) {
		$GLOBALS['args']['user_id']=strtoupper(substr(dataCleaner($_POST['b82c70d9c0d87882bcb6551a8e70da84eca9472e2817ca61ab0ecdc885e6e0fa']), 0, 11));
		$GLOBALS['args']['pass']=md5("passmat".dataCleaner($_POST['3fc10ee46d843e80074879c625e74e4f19147c5b9c1af3d2df41c251d9a4afa9']));
		//echo $GLOBALS['args']['pass'];

	}else{
		$argsMissing=true;
	}
	return !$argsMissing;
}

function authentication(){
	require $_SERVER['DOCUMENT_ROOT']."/api/security/dbusers/security_data_security.php";
	$stmtAuthentication->execute();
	$stmtAuthentication->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtAuthentication->fetchAll();
	$con=null;
	if (count($result)>0) {
		$GLOBALS['authentication']=$result[0];
		$GLOBALS['authentication']['pass']=true;
		return TRUE;
	}else{
		return FALSE;
	}
}

function userTokenExists(){
	require $_SERVER['DOCUMENT_ROOT']."/api/security/dbusers/token_manager_con.php";
	$stmtCheckTokenExists->execute();
	$stmtCheckTokenExists->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtCheckTokenExists->fetchAll();
	$con=null;
	if (count($result)>0) {
		return TRUE;
	}else{
		return FALSE;
	}
}

function updateToken(){
	require $_SERVER['DOCUMENT_ROOT']."/api/security/dbusers/token_manager_con.php";
	if($stmtUpdateToken->execute()){
		$con=null;
		return TRUE;
	}else{
		$con=null;
		return FALSE;
	}
}

function insertToken(){
	require $_SERVER['DOCUMENT_ROOT']."/api/security/dbusers/token_manager_con.php";
	if ($stmtInsertToken->execute()) {
		$con=null;
		return TRUE;
	}
	else{
		$con=null;
		return FALSE;
	}
}


function checkToken(){
	require $_SERVER['DOCUMENT_ROOT']."/api/security/dbusers/token_manager_con.php";
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

if (checkHeaders()) {
	if (checkArguments()) {
		if (authentication()) {
			$GLOBALS['response']['user_id']=$GLOBALS['authentication']['user_id'];
			$GLOBALS['response']['access_token']=md5(date("H:i:m:s").md5(date("H:i:m:s")));
			$GLOBALS['args']['access_token']=$GLOBALS['response']['access_token'];
			$GLOBALS['response']['user_data']=$GLOBALS['authentication'];
			if (userTokenExists()) {
				updateToken();
			}else{
				insertToken();
			}
		}else{
			$GLOBALS['response']['error']=TRUE;
			$GLOBALS['response']['error_code']="IVP";
			$GLOBALS['response']['error_message']="Invalid Password";
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

header('Content-Type: application/json');
echo json_encode($GLOBALS['response']);
