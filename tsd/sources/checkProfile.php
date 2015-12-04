<?php
	include "../../class/connect.class.php";
	
	$con = new getConnection();
	$db = $con->PDO();
	
	if(isset($_POST["cid"])){
		$cid = $_POST["cid"];
		$appId = $_POST["appId"];
		
		$checker = "";
		$query = $db->query("SELECT *FROM tbl_meter_profile WHERE appId = '$appId' AND cid = '$cid'");
		$query2 = $db->query("SELECT *FROM tbl_transactions WHERE appId = '$appId' ORDER BY tid DESC LIMIT 1");
		$query3 = $db->query("SELECT *FROM tbl_app_service a
							  LEFT OUTER JOIN tbl_service b ON a.serviceId = b.serviceId
							  WHERE a.appId = '$appId'");
		$type = "";		  
		if($query3->rowCount() > 0){
			foreach($query3 as $row3){
				$type .= $row3["serviceCode"];
			}
		}

		if($query2->rowCount() > 0){
			foreach($query2 as $row2){
				if($row2["status"] == 7 && $row2["action"] == 0 ){
					if($type == "RC" || $type == "RCCM" || $type == "CM"){
						$checker = 2;
					}
					else if($query->rowCount() > 0)
						$checker = 1;
					else
						$checker = 0;
				}
			}
		}
		
		echo $checker;
		// echo "1";
	}
?>