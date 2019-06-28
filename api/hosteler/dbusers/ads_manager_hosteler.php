<?php
$servername="localhost";
$username="root";
$password="";
$dbname="myhostelapp";

$con=NULL;
try{
	$con=new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	$con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

	$stmtgetActiveAds=$con->prepare("SELECT id, intent, action, provider, banner_url, alt FROM advertisements where active=1");


}catch(PDOException $e){
	echo "Connection Failed".$e;
}

?>
