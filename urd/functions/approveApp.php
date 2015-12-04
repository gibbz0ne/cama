<?php
	include "../../class/connect.class.php";
	
	$con = new getConnection();
	$db = $con->PDO();

	function reconnect($account, $db){
		$recon = $db->prepare("UPDATE consumers SET Status = ? WHERE AccountNumber = ?");
		$recon->execute(array("A", $account));
	}
	
	function updateTransaction($account, $tid, $db){
		$update = $db->prepare("UPDATE tbl_transactions SET action = ? WHERE tid = ? AND status = ?");
		$update->execute(array(1, $tid, 8));
		
		$update = $db->prepare("UPDATE tbl_temp_consumers SET flag = ? WHERE AccountNumberT = ?");
		$update->execute(array(1, $account));
	}
	
	function updateMeter($account, $db, $meter){
		$cm = $db->prepare("UPDATE consumers SET MeterNumber = ? WHERE AccountNumber = ?");
		$cm->execute(array(1, $meter, $account));
	}
	
	function getConsumer($db, $account){
		$query = $db->query("SELECT * FROM consumers WHERE AccountNumber = '$account'");
		$r = $query->fetch(PDO::FETCH_ASSOC);
		
		return $r["MeterNumber"];
	}
	
	if(isset($_POST["acctNo"])){
		$type = $_POST["type"];
		$acctNo = $_POST["acctNo"];
		$query = $db->query("SELECT *FROM tbl_temp_consumers a 
							LEFT OUTER JOIN tbl_transactions b ON a.cid = b.cid 
							LEFT OUTER JOIN tbl_meter_profile c ON a.cid = c.cid
							WHERE a.AccountNumberT = '$acctNo' ORDER BY tid DESC LIMIT 1");
		
		$row = $query->fetchAll(PDO::FETCH_ASSOC);
		
		$query2 = $db->query("SELECT *FROM tbl_meter_profile WHERE AccountNumber = '$acctNo' AND currentMeter = '1'");
		$row2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		echo $row[0]["tid"]." ";
		echo $row[0]["mSerial"]." ";
		echo $row2[0]["mSerial"];
		// try{
			// $db->beginTransacation();
			// if($type == "NC"){
				// $insert = $db->prepare("INSERT INTO consumers (AccountNumber, AccountName, Address, Barangay, Branch, Municipality, CustomerType, bapa, Status, Multiplier, MeterNumber)
										// VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
				// $insert->execute(array($row[0]["AccountNumberT"], $row[0]["AccountNameT"], $row[0]["AddressT"], 
									   // $row[0]["BarangayT"], $row[0]["BranchT"], $row[0]["MunicipalityT"], 
									   // $row[0]["CustomerTypeT"], ($row[0]["bapaT"] == 0 ? "NO" : "YES"), "A", $row[0]["multiplier"], $row["mSerial"]));
				// updateTransaction($acctNo, $row[0]["tid"], $db);
				
			// } else if($type == "RC"){
				// reconnect($acctNo, $db);
				// updateTransaction($acctNo, $row[0]["tid"], $db);
			// } else if($type == "RCCM"){
				// reconnect($acctNo, $db);
				// updateMeter($acctNo, $db, $row[0]["mSerial"]);
				// updateTransaction($acctNo, $row[0]["tid"], $db);
			// } else if($type == "CM"){
				// updateMeter($acctNo, $db, $row[0]["mSerial"]);
				// updateTransaction($acctNo, $row[0]["tid"], $db);
			// }
			// $db->commit();
			// echo "1";
		// } catch(PDOException $e){
			// $db->rollBack();
		// }
	}
?>