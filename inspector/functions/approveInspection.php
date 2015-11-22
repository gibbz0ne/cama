<?php
	include "../../class/connect.class.php";
	$id = $_SESSION["userId"];
	$con = new getConnection();
	$db = $con->PDO();
	
	$tid = explode("-", $_POST["tid"]);
	$transaction_id = $_POST["tid"];

	if(isset($_POST["pType"])){
		$pType = $_POST["pType"];
		$rating = strtoupper($_POST["rating"]);
		$etype = $_POST["etype"];
		$eSize = strtoupper($_POST["eSize"]);
		$wSize = strtoupper($_POST["wSize"]);
		$servicePole = strtoupper($_POST["servicePole"]);
		$length = strtoupper($_POST["length"]);
		$totalVa = $_POST["totalVa"];
		$meter = strtoupper($_POST["meter"]);
		$mClass = $_POST["mClass"];
		$station = $_POST["station"];
		$feeder = $_POST["feeder"];
		$phase = $_POST["phase"];
		$inspectedBy = strtoupper($_POST["inspectedBy"]);
		$iRemarks = strtoupper($_POST["iRemarks"]);
		$date = $_POST["date"];
		$cid = $_POST["cid"];
		$appId = $_POST["appId"];
		
		$i = 1;
		$status = 2;
		
		$processed = $t = 0;
		$transaction = $db->query("SELECT * FROM tbl_transactions WHERE tid LIKE '%$tid[0]%'ORDER BY tid DESC LIMIT 1");
		if($transaction->rowCount() > 0){
			foreach($transaction as $row){
				$d = explode("-", $row["tid"]);
				$incr = $d[1]+1;
				$t = $d[0]."-".$incr;
				$processed = $row["processedBy"];
			}
		}
		
		$res = $db->query("SELECT AccountNumberT FROM tbl_temp_consumers WHERE cid = '$cid'");
		$rowT = $res->fetchAll(PDO::FETCH_ASSOC);

		// if($rowT[0]["AccountNumber"]) {
			// $status = 3;
		// }
		$query = $db->query("SELECT *FROM tbl_inspection ORDER BY inspectionId DESC limit 1");
		
		if($query->rowCount() > 0){
			foreach($query as $row){
				$i = $row["inspectionId"]+1;
			}
		}
		
		$inspection_data = array($i, $appId, $cid, $inspectedBy, $iRemarks,	$date);
		$type_data = array($i, $pType, $rating, $wSize, $etype, $eSize, $length, $servicePole);
		$meter_data = array($i, $meter, $mClass, $totalVa, $station, $feeder, $phase);
		$update_data = array(1, $id, date("Y-m-d H:i:s"), $iRemarks, $transaction_id);
		$trans_data = array($t, $appId, $cid, $status, $processed, date("Y-m-d H:i:s"));
		try{
			$db->beginTransaction();
			
			$inspection = $db->prepare("INSERT INTO tbl_inspection 
										(inspectionId, appId, cid, inspectedBy, iRemarks, dateInspected)
										VALUES
										(?, ?, ?, ?, ?, ?)");
			$inspection->execute($inspection_data);
			
			$type = $db->prepare("INSERT INTO tbl_inspection_type
									(inspectionId, protectionId, pRating, wireSize, eid, eSize, length, servicePole)
									VALUES
									(?, ?, ?, ?, ?, ?, ?, ?)");
			$type->execute($type_data);
			
			$meter = $db->prepare("INSERT INTO tbl_inspection_meter
									(inspectionId, meterForm, meterClass, totalVa, subId, feedId, phase)
									VALUES
									(?, ?, ?, ?, ?, ?, ?)");
			$meter->execute($meter_data);						
									
			$update = $db->prepare("UPDATE tbl_transactions SET action = ?, approvedBy = ?, dateApproved = ?, remarks = ? WHERE tid = ?");
			$update->execute($update_data);

			$insert = $db->prepare("INSERT INTO tbl_transactions 
									(tid, appId, cid, status, processedBy, dateProcessed)
									VALUES
									(?, ?, ?, ?, ?, ?)");
			$insert->execute($trans_data);
			
			echo "1";
			$db->commit();
		} catch(PDOException $e){
			$db->rollBack();
			echo $e;
		}
	}
?>