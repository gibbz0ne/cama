<?php
	include "../../class/connect.class.php";
	
	$con = new getConnection();
	$db = $con->PDO();
	
	if(isset($_POST["acctNo"])){
		$appId = $_POST["appId"];
		$cid = $_POST["cid"];
		$acctNo = $_POST["acctNo"];
		$date = $_POST["date"];
		$accomplishedBy = $_POST["accomplishedBy"];
		$mReading = $_POST["mReading"];
		$mBrand = $_POST["mBrand"];
		$mClass = $_POST["mClass"];
		$mSerial = $_POST["mSerial"];
		$mERC = $_POST["mERC"];
		$mLabSeal = $_POST["mLabSeal"];
		$mTerminal = $_POST["mTerminal"];
		$multiplier = $_POST["multiplier"];
		$tid = explode("-", $_POST["tid"]);
		
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
		
		$query = $db->query("SELECT *FROM tbl_meter_profile WHERE AccountNumber = '$acctNo' AND mSerial = '$mSerial'");
		try{
			$db->beginTransaction();
			
			if($query->rowCount() > 0){
				$update = $db->prepare("UPDATE tbl_meter_profile SET currentMeter = ?, mERC = ?, mLabSeal = ?, mTerminal = ?, multiplier = ?, mReading = ?, mBrand = ?, mClass = ?
										WHERE mSerial = ? AND AccountNumber = ?");
				$update->execute(array(0, $mERC, $mLabSeal, $mTerminal, $multiplier, $mReading, $mBrand, $mClass, $mSerial, $acctNumber));
			} else{
				$insert = $db->prepare("INSERT INTO tbl_meter_profile (appId, cid, AccountNumber, mReading, mBrand, mClass, mSerial, mERC, mLabSeal, mTerminal, multiplier) 
										VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
				$insert->execute(array($appId, $cid, $acctNo, $mReading, $mBrand, $mClass, $mSerial, $mERC, $mLabSeal, $mTerminal, $multiplier));
			}
			
			$update = $db->prepare("UPDATE tbl_transactions SET action = ?, dateApproved = ? WHERE status = ? AND action = ? AND appId = ? AND cid = ?");
			$update->execute(array(1, date("Y-m-d H:i:s"), 7, 0, $appId, $cid));
			
			$insert = $db->prepare("INSERT INTO tbl_transactions (tid, appId, cid, status, processedBy, dateProcessed) VALUES (?, ?, ?, ?, ?, ?)");
			$insert->execute(array($t, $appId, $cid, 8, $processed, date("Y-m-d H:i:s")));
			
			$updateWo = $db->prepare("UPDATE tbl_work_order SET accomplishedBy = ?, dateInstalled = ? WHERE appId = ?");
			$updateWo->execute(array($accomplishedBy, $date, $appId));
			
			$db->commit();
			echo "1";
		} catch(PDOException $e){
			$db->rollBack();
			echo $e;
		}
	}
?>