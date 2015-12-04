<?php
    include "../../class/connect.class.php";

    $con = new getConnection();
    $db = $con->PDO();
    $ctr = 1;
    $list = array();
	$branch = $_SESSION["branch"];
	if($branch == "B1")
		$wo = "WOE";
	else if($branch == "B2")
		$wo = "WOM-MAIN";
		
	$query = $db->query("SELECT *FROM tbl_mr WHERE isSend = '1' ORDER BY mrNo DESC");
	
	if($query->rowCount() > 0){
		foreach($query as $row){
			$query2 = $db->query("SELECT *FROM tbl_mr_content WHERE mrNo = '".$row["mrNo"]."'");
            $query3 = $db->query("SELECT *FROM tbl_mr_wo WHERE mrNo = '".$row["mrNo"]."' AND wo LIKE '%$wo%'");
			
			if($query3->rowCount() > 0){
				foreach($query3 as $row2)
					$appId = $row2["appId"];
				// $query4 = $db->query("SELECT *FROM tbl_transactions WHERE status = '4' AND appId = '$appId'");
				// if($query4->rowCount() > 0){
					// foreach($db->query("SELECT *FROM tbl_meter_profile WHERE cid"))
					$list[] = array("ctr" => $ctr,
									"mrNo" => $row["mrNo"],
									"purpose" => $row["mrPurpose"],
									"date" => $row["mrDate"],
									"items" => $query2->rowCount(),
									"wos" => $query3->rowCount()
									);
					$ctr++;
				
			// }
			}
		}
		echo json_encode($list);
	}
?>