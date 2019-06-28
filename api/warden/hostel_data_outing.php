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
		&&$GLOBALS['headers']['RCPTEQ']=='bUIxadsjUTgyNjMyMUY0') {
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
		$GLOBALS['args']['access_token']=dataCleaner($_POST['3fc10ee46d843e80074879c625e74e4f19147c5b9c1af3d2df41c251d9a4afa9']);
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
	if (count($result)>0 && $result[0]['block']==0 &&  $result[0]['user_type']=='W') {
		$GLOBALS['database']['tokens_table']=$result[0];
		return TRUE;
	}else{
		return FALSE;
	}
}

function getHostelData(){
	require $_SERVER['DOCUMENT_ROOT']."/api/warden/dbusers/user_data_warden.php";
	$stmtHostelDataWithOuting->execute();
	$stmtHostelDataWithOuting->setFetchMode(PDO::FETCH_ASSOC);
	$result=$stmtHostelDataWithOuting->fetchAll();
	$con=null;
	$result;
	$response=array();
	for ($i=0; $i <count($result) ; $i++) {
		$response[$i]['user_id']=$result[$i]['user_id'];
		$response[$i]['name']=$result[$i]['name'];
		$response[$i]['room']=$result[$i]['room'];
		$response[$i]['email']=$result[$i]['email'];
		$response[$i]['phone']=$result[$i]['phone'];
		$response[$i]['parent_no']=$result[$i]['parent_no'];

		if($result[$i]['hash']!=NULL){
			$response[$i]['active_outing']=TRUE;

			$response[$i]['outing_details']['hash']=$result[$i]['hash'];
			$response[$i]['outing_details']['otp']=$result[$i]['otp'];
			$response[$i]['outing_details']['type']=$result[$i]['type'];
			$response[$i]['outing_details']['status']=$result[$i]['status'];
			$response[$i]['outing_details']['user_id']=$result[$i]['user_id'];
			$response[$i]['outing_details']['place']=$result[$i]['place'];
			$response[$i]['outing_details']['purpose']=$result[$i]['purpose'];
			$response[$i]['outing_details']['going_with']=$result[$i]['going_with'];
			$response[$i]['outing_details']['phone']=$result[$i]['phone'];

			if ($result[$i]['type']=="L") {
				$response[$i]['outing_details']['parent_no']=$result[$i]['parent_no'];
				$response[$i]['outing_details']['dt_exp_out']=$result[$i]['dt_exp_out'];
				$response[$i]['outing_details']['d_exp_in']=$result[$i]['d_exp_in'];

				if ($result[$i]['status']>=2) {
					$response[$i]['outing_details']['sign_warden']=$result[$i]['sign_warden'];
					$response[$i]['outing_details']['remarks']=$result[$i]['remarks'];

				}
				if ($result[$i]['status']==3) {
					$response[$i]['outing_details']['dt_out']=$result[$i]['dt_out'];
					$response[$i]['outing_details']['sign_sec_out']=$result[$i]['sign_sec_out'];
				}

			}else{
				if ($result[$i]['status']==3) {
					$response[$i]['outing_details']['dt_out']=$result[$i]['dt_out'];
					$response[$i]['outing_details']['sign_sec_out']=$result[$i]['sign_sec_out'];
				}
			}
		}else{
			$response[$i]['active_outing']=FALSE;
		}
	}
	$GLOBALS['response']['hostelData']=$response;
}

if (checkHeaders()) {
	if (checkArguments()) {
		if (checkToken()) {
			getHostelData();
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
