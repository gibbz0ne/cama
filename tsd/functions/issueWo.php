<?php
	include "../../class/connect.class.php";
	
	$con = new getConnection();
	$db = $con->PDO();

	$uid = $_SESSION["userId"];
	$branch = $_SESSION["branch"];
	$y = date("y");
	$ext = $wom = $num = "";
	if($branch == "B1"){
		$ext = "WOE".date("y")."-T";
		$num = 3;
	}
	if($branch == "B2"){
		$ext = "WOM-MAIN-".date("y");
		$num = 4;
	}
	
	if($_POST["workNo"] == ""){
		$query = $db->query("SELECT *FROM tbl_work_order WHERE wo LIKE '%$ext%' ORDER BY wo DESC LIMIT 1");
		if($query->rowCount() > 0){
			foreach($query as $row)
				$wo = explode("-", $row["wo"]);
				$series = intval($wo[$num])+1;
				// echo $series;
				if(strlen($series) == 1){
					$wom = "APEC-".$ext."-000".$series;
				} else if(strlen($series) == 2){
					$wom = "APEC-".$ext."-00".$series;
				} else if(strlen($series) == 3){
					$wom = "APEC-".$ext."-0".$series;
				} else{
					$wom = "APEC-".$ext."-".$series;
				}
		} else{
			$wom = "APEC-".$ext."-0001";
		}
	}else{
		if(strlen($_POST["workNo"]) == 1)
			$wom = "APEC-".$ext."-000".$_POST["workNo"];
		else if(strlen($_POST["workNo"]) == 2)
			$wom = "APEC-".$ext."-00".$_POST["workNo"];
		else if(strlen($_POST["workNo"]) == 3)
			$wom = "APEC-".$ext."-0".$_POST["workNo"];
		else
			$wom = "APEC-".$ext."-".$_POST["workNo"];
	}
	if(isset($_POST["appId"])){
		$appId = $_POST["appId"];
		$scope = strtoupper($_POST["scope"]);
		$cid = $_POST["cid"];
		$tid = $_POST["tid"];
		
		$processed = $t = 0;
		$ti = explode("-", $tid);
		$transaction = $db->query("SELECT * FROM tbl_transactions WHERE tid LIKE '%$ti[0]%'ORDER BY tid DESC LIMIT 1");
		if($transaction->rowCount() > 0){
			foreach($transaction as $row){
				$d = explode("-", $row["tid"]);
				$incr = $d[1]+1;
				$t = $d[0]."-".$incr;
				$processed = $row["processedBy"];
			}
		}
		
		try{
			$db->beginTransaction();
			
			$insert = $db->prepare("INSERT INTO tbl_work_order (wo, appId, cid, woDate, scope) VALUES (?, ?, ?, ?, ?)");
			$insert->execute(array($wom, $appId, $cid, date("Y-m-d")." ".date("H:i:s"), $scope));
			
			$update = $db->prepare("UPDATE tbl_transactions SET action = ?, approvedBy = ?, dateApproved = ? WHERE tid = ?");
			$update->execute(array(1, $uid, date("Y-m-d H:i:s"), $tid));

			$insert = $db->prepare("INSERT INTO tbl_transactions (tid, appId, cid, status, processedBy, dateProcessed)
								VALUES(?, ?, ?, ?, ?, ?)");
			$insert->execute(array($t, $appId, $cid, 5, $processed, date("Y-m-d H:i:s")));
			
			$db->commit();
			echo "1";
		} catch(PDOException $e){
			$db->rollBack();
			echo $e;
		}
	}
?>