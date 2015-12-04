<?php
    include "../../class/connect.class.php";

    $con = new getConnection();
    $db = $con->PDO();
    $ctr = 1;
    $list = array();
	
	if(isset($_GET["mr"])){
		$mr = $_GET["mr"];
		$ctr = 1;
		$query = $db->query("SELECT *FROM tbl_mr_wo a
							 LEFT OUTER JOIN tbl_applications b ON a.appId = b.appId
							 LEFT OUTER JOIN tbl_temp_consumers c ON b.cid = c.cid
							 WHERE a.mrNo = '$mr'");
		
		foreach($query as $row){
			// echo $row["munId"];
			$appId = $row["appId"];
			$mReading = $mBrand = $mClass = $mSerial = $mERC = $mLabSeal = $mTerminal = $multiplier = "";
			$omReading = $omBrand = $omClass = $omSerial = $omERC = $omLabSeal = $omTerminal = $omultiplier = "";
			$meterProfile = $db->query("SELECT *FROM tbl_meter_profile WHERE cid = '".$row["cid"]."' AND appId = '".$row["appId"]."' AND currentMeter = 1 ORDER BY mid DESC LIMIT 1");
			$meterProfile1 = $db->query("SELECT *FROM tbl_meter_profile WHERE cid = '".$row["cid"]."' AND appId = '".$row["appId"]."' AND currentMeter = 0 ORDER BY mid DESC LIMIT 1");
			
			
			foreach($db->query("SELECT *FROM tbl_transactions WHERE appId = '$appId' ORDER BY tid DESC LIMIT 1") as $row3){
				// if($row3["status"] == 7 && $row3["action"] == 0){
					// if($meterProfile->rowCount() > 0){
						foreach($meterProfile as $r){
							$mReading = $r["mReading"];
							$mBrand = $r["mBrand"];
							$mClass = $r["mClass"];
							$mSerial = $r["mSerial"];
							$mERC = $r["mERC"];
							$mLabSeal = $r["mLabSeal"];
							$mTerminal = $r["mTerminal"];
							$multiplier = $r["multiplier"];
						}
						foreach($meterProfile1 as $r){
							$omReading = $r["mReading"];
							$omBrand = $r["mBrand"];
							$omClass = $r["mClass"];
							$omSerial = $r["mSerial"];
							$omERC = $r["mERC"];
							$omLabSeal = $r["mLabSeal"];
							$omTerminal = $r["mTerminal"];
							$omultiplier = $r["multiplier"];
						}
						$status = "NOT INSTALLED";
						if($row3["status"] == 8) $status = "INSTALLED";
						
						$list[] = array(
							"ctr1" => $ctr,
							"status" => $status,
							"acctNo" => $row["AccountNumberT"],
							"consumerName" => $row["AccountNameT"],
							"address" => $row["AddressT"],
							"cid" => $row["cid"],
							"appId" => $row["appId"],
							"tid" => $row3["tid"],
							"mReading" => $mReading,
							"mBrand" => $mBrand,
							"mClass" => $mClass,
							"mSerial" => $mSerial,
							"mERC" => $mERC,
							"mLabSeal" => $mLabSeal,
							"mTerminal" => $mTerminal,
							"multiplier" => $multiplier,
							"omReading" => $omReading,
							"omBrand" => $omBrand,
							"omClass" => $omClass,
							"omSerial" => $omSerial,
							"omERC" => $omERC,
							"omLabSeal" => $omLabSeal,
							"omTerminal" => $omTerminal,
							"omultiplier" => $omultiplier,);
						$ctr++;
					// }
				// }
			}
			
		}
		
		echo json_encode($list);
	}
	if(isset($_POST["cid"])){
		$cid = $_POST["cid"];
		$ctr = 1;
		foreach($db->query("SELECT *FROM tbl_applications a
							LEFT OUTER JOIN tbl_temp_consumers b ON a.cid = b.cid 
							WHERE a.cid = '$cid'") as $row){

			$mReading = $mBrand = $mClass = $mSerial = $mERC = $mLabSeal = $mTerminal = $multiplier = "";
			foreach($db->query("SELECT *FROM tbl_meter_profile WHERE cid = '$cid' AND appId = '".$row["appId"]."'") as $r){
				$mReading = $r["mReading"];
				$mBrand = $r["mBrand"];
				$mClass = $r["mClass"];
				$mSerial = $r["mSerial"];
				$mERC = $r["mERC"];
				$mLabSeal = $r["mLabSeal"];
				$mTerminal = $r["mTerminal"];
				$multiplier = $r["multiplier"];
			}
			
			$list = array(
				"acctNo" => $row["AccountNumberT"],
				"consumerName" => $row["AccountNameT"],
				"address" => $row["AddressT"],
				"cid" => $row["cid"],
				"mReading" => $mReading,
				"mBrand" => $mBrand,
				"mClass" => $mClass,
				"mSerial" => $mSerial,
				"mERC" => $mERC,
				"mLabSeal" => $mLabSeal,
				"mTerminal" => $mTerminal,
				"multiplier" => $multiplier
			);
			
			echo json_encode($list);
		}
	}
?>