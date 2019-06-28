<?php
$servername="localhost";
$username="root";
$password="";
$dbname="myhostelapp";

$con=NULL;
try{
	$con=new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	$con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

		$stmtCheckHostelExists=$con->prepare("SELECT id FROM hostels where name=:var_name LIMIT 1");
		$stmtCheckHostelExists->bindParam(':var_name', $hostelName);

		$stmtGetHostelById=$con->prepare("SELECT * FROM hostels where name=:var_name LIMIT 1");
		$stmtGetHostelById->bindParam(':var_name', $hostel);

		$stmtAddHostel=$con->prepare("INSERT INTO hostels values('', :var_type,:var_name)");
		$stmtAddHostel->bindParam(':var_type', $type);
		$stmtAddHostel->bindParam(':var_name', $hostelName);


}catch(PDOException $e){
	echo "Connection Failed".$e;
}



?>
