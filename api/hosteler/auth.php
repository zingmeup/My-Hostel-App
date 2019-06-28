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
		if ($GLOBALS['headers']['RCPTEQ']=='YlVJeE5taEdVR2hqVVRneU5qTXlNVVkw') {
			return true;
		}
	}
	return false;
}

function checkArguments(){
	$argsMissing=false;
	if (isset($_POST['6551a8e70da8'])&&!empty($_POST['6551a8e70da8'])
	&&isset($_POST['78acfda158eb'])&&!empty($_POST['78acfda158eb'])) {
		$GLOBALS['args']['user_id']=strtoupper(substr(dataCleaner($_POST['6551a8e70da8']), 0, 11));
		$GLOBALS['args']['pass']=dataCleaner($_POST['78acfda158eb']);
	}else{
		$argsMissing=true;
	}
	return !$argsMissing;
}


function authentication(){
	$response=json_decode(file_get_Contents($_SERVER['DOCUMENT_ROOT']."api/hosteler/mock/login.json"),true);
	if(array_key_exists('Message', $response)){
		if ($response['Message']=="Success") {
			$GLOBALS['authentication']['user_id']=$response['LoginId'];
			$GLOBALS['authentication']['access_token']=$response['AccessToken'];
			$GLOBALS['args']['access_token']=$response['AccessToken'];
			$GLOBALS['authentication']['user_type']=$response['UserType'];
			if ($GLOBALS['authentication']['user_type']=='S') {
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
	$response=json_decode(file_get_Contents($_SERVER['DOCUMENT_ROOT']."api/hosteler/mock/userInfo.json"),true);
	if(array_key_exists('Message', $response)){
		if ($response['Message']=="Invalid Acess Token"){
			return FALSE;
		}
	}else{
		if (array_key_exists('HostelName', $response)){
			$GLOBALS['getUserInfo']['user_id']=$response['UID'];
			$GLOBALS['getUserInfo']['user_type']=$response['Type'];
			$GLOBALS['getUserInfo']['gender']=$response['Gender'];
			$Program=explode("(", $response['ProgramName']);
			$GLOBALS['getUserInfo']['branch']=$Program[0];
			$GLOBALS['getUserInfo']['course']=str_replace(")", "", $Program[1]);
			$GLOBALS['getUserInfo']['section']=$response['SECTION'];
			$GLOBALS['getUserInfo']['name']=$response['Name'];
			$GLOBALS['getUserInfo']['hostel']=$response['HostelName'];
			$GLOBALS['getUserInfo']['hostel_id']=registerHostel($response['HostelName'], $response['Gender'][0]);
			$GLOBALS['getUserInfo']['room']=$response['RoomNo'];
			$GLOBALS['getUserInfo']['img']=$response['Snap'];
			return TRUE;
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
	require $_SERVER['DOCUMENT_ROOT']."/api/hosteler/dbusers/user_data_hosteler.php";
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
	require $_SERVER['DOCUMENT_ROOT']."/api/hosteler/dbusers/user_data_hosteler.php";
	if($stmtUpdateUser->execute()){
		return TRUE;
	}else{
		return FALSE;
	}
}

function insertUserData(){
	require $_SERVER['DOCUMENT_ROOT']."/api/hosteler/dbusers/user_data_hosteler.php";
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
	require $_SERVER['DOCUMENT_ROOT']."/api/hosteler/dbusers/user_data_hosteler.php";
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

echo json_encode($GLOBALS['response']);
