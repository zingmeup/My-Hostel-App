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
		if ($GLOBALS['headers']['RCPTEQ']=='U1hoYWFtaDFZMWxWYTBSVldWTXdjRmxV') {
			return true;
		}
	}
	return false;
}

function checkArguments(){
	$argsMissing=false;
	if (isset($_POST['6551a8e70da8'])&&!empty($_POST['6551a8e70da8'])
	&&isset($_POST['80074879c625'])&&!empty($_POST['80074879c625'])) {
		$GLOBALS['args']['user_id']=strtoupper(substr(dataCleaner($_POST['6551a8e70da8']),0,11));
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

function checkCancellable(){
	require $_SERVER['DOCUMENT_ROOT']."/api/hosteler/dbusers/outing_manager_hosteler.php";
	$stmtCheckStatus->execute();
	$stmtCheckStatus->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtCheckStatus->fetchAll();
	$con=null;
	if (count($result)>0) {
		$GLOBALS['checkStatus']=$result[0];
		if ($result[0]['outing_type']==NULL) {
			$GLOBALS['response']['request']="success";
			return TRUE;
		}else{
			if ($result[0]['status']==2 ||$result[0]['status']==1 ) {
				$GLOBALS['args']['hash']=md5($result[0]['otp']."cniGEL".$result[0]['outing_type']);
				return TRUE;
			}else {
				return FALSE;
			}
		}
	}
	return FALSE;
}

function cancelOutingRequest(){
	require $_SERVER['DOCUMENT_ROOT']."/api/hosteler/dbusers/outing_manager_hosteler.php";
	if($cancelOutingRequest->execute()){
		if($stmtsetStatusToNull->execute()){
			return TRUE;
		}
	}
	return FALSE;

}

if (checkHeaders()) {
	if (checkArguments()) {
		if (checkToken()) {
			if (checkCancellable()) {
				if (cancelOutingRequest()) {
					$GLOBALS['response']['request']="success";
				}
			}else{
				$GLOBALS['response']['error']=true;
				$GLOBALS['response']['error_code']="IVS";
				$GLOBALS['response']['error_message']="You can not cancel now";
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
