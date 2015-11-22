<?php
	include "../../class/connect.class.php";
	include "../../class/accountNum.class.php";
	
	$con = new getConnection();
	$processNext = new getNextNum();
	
	$db = $con->PDO();
	$id = $_SESSION["userId"];

	if(isset($_POST["cid"])){
		$cid = $_POST["cid"];
		$acctNum = $_POST["acct"];
		$appId = $_POST["appId"];
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
		
		$query = $db->query("SELECT *FROM consumers WHERE AccountNumber = '$acctNum'");
		$query2 = $db->query("SELECT *FROM tbl_temp_consumers WHERE AccountNumberT = '$acctNum'");
		if($acctNum == ""){
			echo "Account Number Invalid";
		}else if(strlen($acctNum)<15){
			echo "Account Number Invalid";
		}else if($query->rowCount()>0 || $query2->rowCount()>0)
			echo "Account Number Exist";
		else{
			try{
				$db->beginTransaction();
				$insert = $db->prepare("UPDATE tbl_temp_consumers SET AccountNumberT = ? WHERE cid = ?");
				$insert->execute(array($acctNum, $cid));

				$update = $db->prepare("UPDATE tbl_transactions SET action = ?, approvedBy = ?, dateApproved = ?, remarks = ? WHERE tid = ?");
				$update->execute(array(1, $id, date("Y-m-d H:i:s"), null, $tid));

				$insert = $db->prepare("INSERT INTO tbl_transactions (tid, appId, cid, status, processedBy, dateProcessed)
									VALUES(?, ?, ?, ?, ?, ?)");
				$insert->execute(array($t, $appId, $cid, 4, $processed, date("Y-m-d H:i:s")));
				$db->commit();
				echo "1"; 
			} catch(PDOException $e){
				$db->rollBack();
				echo $e;
			}
		}
	}
?>