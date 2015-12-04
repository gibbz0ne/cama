<?php
	include "../../class/connect.class.php";
	
	$con = new getConnection();
	$db = $con->PDO();
	$list = array();
	
	$query = $db->query("SELECT *FROM tbl_meter_brand");
	if($query->rowCount() > 0){
		foreach($query as $row){
			array_push($list, array("brandName" => $row["brandName"]));
		}
		
		echo json_encode($list);
	}
?>