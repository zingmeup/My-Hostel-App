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
		&&$GLOBALS['headers']['RCPTEQ']=='bUI768UGhjUTgyNjMyMUY0') {
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
		$GLOBALS['args']['pass']=dataCleaner($_POST['3fc10ee46d843e80074879c625e74e4f19147c5b9c1af3d2df41c251d9a4afa9']);
	}else{
		$argsMissing=true;
	}
	return !$argsMissing;
}

function authentication(){
	$response=json_decode(file_get_Contents($_SERVER['DOCUMENT_ROOT']."api/warden/mock/login.json"),true);
	if(array_key_exists('Message', $response)){
		if ($response['Message']=="Success") {
			$GLOBALS['authentication']['user_id']=$response['LoginId'];
			$GLOBALS['authentication']['access_token']=$response['AccessToken'];
			$GLOBALS['args']['access_token']=$response['AccessToken'];
			$GLOBALS['authentication']['user_type']=$response['UserType'];
			if ($GLOBALS['authentication']['user_type']=='E') {
				return TRUE;
			}
		}
	}
	return FALSE;
}

function userTokenExists(){
	require $_SERVER['DOCUMENT_ROOT']."/api/dbusers/token_manager_con.php";
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
	require $_SERVER['DOCUMENT_ROOT']."/api/dbusers/token_manager_con.php";
	if($stmtUpdateToken->execute()){
		$con=null;
		return TRUE;
	}else{
		$con=null;
		return FALSE;
	}
}

function insertToken(){
	require $_SERVER['DOCUMENT_ROOT']."/api/dbusers/token_manager_con.php";
	if ($stmtInsertToken->execute()) {
		$con=null;
		return TRUE;
	}
	else{
		$con=null;
		return FALSE;
	}
}

function getUserInfo(){
	$response=json_decode(file_get_Contents($_SERVER['DOCUMENT_ROOT']."api/warden/mock/userInfo.json"),true);
	if(array_key_exists('Message', $response)){
		if ($response['Message']=="Invalid Acess Token"){
			return FALSE;
		}
	}else{
		if (array_key_exists('HostelName', $response)){
			$GLOBALS['getUserInfo']['user_id']=$response['UID'];
			$GLOBALS['getUserInfo']['user_type']=$response['Type'];
			$GLOBALS['getUserInfo']['gender']=$response['Gender'];
			$GLOBALS['getUserInfo']['name']=$response['Name'];
			$GLOBALS['getUserInfo']['hostel']=$response['HostelName'];
			$GLOBALS['getUserInfo']['hostel_id']=registerHostel($response['HostelName'], $response['Gender'][0]);
			$GLOBALS['getUserInfo']['img']=$response['Snap'];
			$GLOBALS['getUserInfo']['status']=1;
			if ($response['Type']=="H") {
				$GLOBALS['getUserInfo']['user_type']="W";
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}
	return FALSE;
}

function registerHostel($hostelName, $type){
	require $_SERVER['DOCUMENT_ROOT']."/api/dbusers/hostel_manager_con.php";
	$stmtCheckHostelExists->execute();
	$stmtCheckHostelExists->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtCheckHostelExists->fetchAll();
	if (!count($result)>0) {
		$stmtAddHostel->execute();
		$id=$con->lastInsertId();
		return $id;

	}else{
		return $result[0]['id'];
	}
}

function userExistsInDB(){
	require $_SERVER['DOCUMENT_ROOT']."/api/warden/dbusers/user_data_warden.php";
	$stmtCheckUseridExists->execute();
	$stmtCheckUseridExists->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtCheckUseridExists->fetchAll();
	$con=null;
	if (count($result)>0) {
		return TRUE;
	}else{
		return FALSE;
	}
}

function updateUserData(){
	require $_SERVER['DOCUMENT_ROOT']."/api/warden/dbusers/user_data_warden.php";
	if($stmtUpdateUser->execute()){
		return TRUE;
	}else{
		return FALSE;
	}
}

function insertUserData(){
	require $_SERVER['DOCUMENT_ROOT']."/api/warden/dbusers/user_data_warden.php";
	if($stmtInsertUser->execute()){
		$con=null;
		return TRUE;
	}else{
		$con=null;
		return FALSE;
	}

}

function checkToken(){
	require $_SERVER['DOCUMENT_ROOT']."/api/dbusers/token_manager_con.php";
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

function getAllUserData(){
	require $_SERVER['DOCUMENT_ROOT']."/api/warden/dbusers/user_data_warden.php";
	$stmtgetUserData->execute();
	$stmtgetUserData->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtgetUserData->fetchAll();
	$con=null;
	if (count($result)>0) {
		$GLOBALS['database']['user_data']=$result[0];
		return TRUE;
	}else{
		return FALSE;
	}

}


if (checkHeaders()) {
	if (checkArguments()) {
		if (authentication()) {
			$GLOBALS['response']['user_id']=$GLOBALS['authentication']['user_id'];
			$GLOBALS['response']['access_token']=$GLOBALS['authentication']['access_token'];
			if (userTokenExists()) {
				updateToken();
			}else{
				insertToken();
			}
			if (getUserInfo()) {
				if (userExistsInDB()) {
					updateUserData();
				}else{
					insertUserData();
				}
			}
			if (checkToken()) {
				getAllUserData();
				$GLOBALS['response']['user_data']=$GLOBALS['database']['user_data'];
			}else{
				$GLOBALS['response']['error']=TRUE;
				$GLOBALS['response']['error_code']="IVT";
				$GLOBALS['response']['error_message']="Invalid Token";
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
