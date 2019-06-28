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
		if ($GLOBALS['headers']['RCPTEQ']=='VTFob1lXRnRhSGRWYldSSlYxWk5kMk5HY') {
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


function getOutingHistory(){
	require $_SERVER['DOCUMENT_ROOT']."/api/hosteler/dbusers/outing_manager_hosteler.php";
	$stmtOutingHistory->execute();
	$stmtOutingHistory->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtOutingHistory->fetchAll();
	$con=null;
	$GLOBALS['OutingHistory']['outing_count']=count($result);
		$GLOBALS['OutingHistory']['outings']=null;
	if (count($result)>0) {
		$GLOBALS['OutingHistory']['outings']=$result;
	}
}

if (checkHeaders()) {
	if (checkArguments()) {
		if (checkToken()) {
			getOutingHistory();
			$GLOBALS['response']['outing_count']=$GLOBALS['OutingHistory']['outing_count'];
			$GLOBALS['response']['outings']=$GLOBALS['OutingHistory']['outings'];
		}else{
			$GLOBALS['response']['error']=TRUE;
			$GLOBALS['response']['error_code']="IVAT";
			$GLOBALS['response']['error_message']="Your services seems to be blocked.\nTry login again.";
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
