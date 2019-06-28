<?php
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
    &&$GLOBALS['headers']['RCPTEQ']=='bUIxNmhGUGhjUTgyNjMyMUY0') {
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

function getOutingsForLeave(){
  require $_SERVER['DOCUMENT_ROOT']."/api/warden/dbusers/outing_manager_warden.php";
  $stmtgetOutingsForLeave->execute();
  $stmtgetOutingsForLeave->setFetchMode(PDO::FETCH_ASSOC);
  $result=$stmtgetOutingsForLeave->fetchAll();
  $con=null;
  $GLOBALS['response']['active_outing']=$result;
}

if (checkHeaders()) {
  if (checkArguments()) {
    if (checkToken()) {
      getOutingsForLeave();
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
