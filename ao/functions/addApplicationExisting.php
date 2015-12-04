<?php
	include "../../class/connect.class.php";
	
	$id = $_SESSION["userId"];
	$con = new getConnection();
	$db = $con->PDO();
	$car = 1;
	
	$branch = $codeB = $_SESSION["branch"];
	$area = $codeA = $_SESSION["area"];
	$codeB[0] = "0";
	$codeA[0] = "0";
	$code = $codeB.$codeA."-".date("y-m");
	
	$user = $_SESSION["username"];
	$type = $_POST["type"];
	$acctNo = $_POST["acctNo"];
	$appId = date("Ymd")."001";
	$cid = $user."-".date("y")[1]."0001";
	$tid = $user."-".date("y")."00001";
	
	$query = $db->query("SELECT *FROM tbl_applications WHERE appCAR LIKE '%$code%' ORDER BY appCAR DESC LIMIT 1");
	if($query->rowCount() > 0){
		foreach($query as $row){
			$e = explode("-", $row["appCAR"]);
			$incr = intval($e[2]+1);
			$car = $e[0]."-".$e[1]."-".$incr;
		}
	} else
		$car = $code."0001";
	
	$query = $db->query("SELECT *FROM tbl_applications ORDER BY appId DESC LIMIT 1");
	
	$consumer = $db->query("SELECT * FROM tbl_temp_consumers WHERE cid LIKE '%$user-".date("y")[1]."%'ORDER BY cid DESC LIMIT 1");
	if($consumer->rowCount() > 0){
		foreach($consumer as $row){
			$d = explode("-", $row["cid"]);
			$incr = intval($d[1]+1);
			$cid = $user."-".$incr;
		}
	}
	
	$transaction = $db->query("SELECT * FROM tbl_transactions WHERE tid LIKE '%$user-".date("y")."%'ORDER BY tid DESC LIMIT 1");
	if($transaction->rowCount() > 0){
		foreach($transaction as $row){
			$d = explode("-", $row["tid"]);
			$incr = $d[1]+1;
			$tid = $user."-".$incr;
		}
	}
	
	if($query->rowCount() > 0){
		foreach($query as $row){
			$d = explode(" ", $row["appDate"]);
			if($d[0] == date("Y-m-d")){
				$checker = $row["appId"][8].$row["appId"][9].$row["appId"][10];
				$incr = intval($checker)+1;
			
				if($incr >= 100 || $incr >= 99){
					$appId = date("Ymd").$incr;
				} else if($incr >= 10){
					if( $incr == 9){
						$appId = date("Ymd")."00".$incr;
					} else{
						$appId = date("Ymd")."0".$incr;
					}
				} else{
					$appId = date("Ymd")."00".$incr;
				}
			} 
		}
	} 
	
	$query = $db->query("SELECT *FROM consumers WHERE AccountNumber = '$acctNo'");
	
	if($query->rowCount() > 0){
		foreach($query as $row){
			try{
				$db->beginTransaction();
				$insert = $db->prepare("INSERT INTO tbl_temp_consumers (cid, AccountNumberT, AccountNameT, AddressT, BarangayT, BranchT, MunicipalityT, CustomerTypeT, bapaT, bookT) 
										VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
				$insert->execute(array($cid, $row["AccountNumber"], $row["AccountName"], $row["Address"], $row["Barangay"], $row["Branch"], $row["Municipality"], $row["CustomerType"], $row["bapa"], $row["BookNumber"]));
				
				$app = $db->prepare("INSERT INTO tbl_applications (appId, cid, appCAR, dateCAR, appDate)
									 VALUES (?, ?, ?, ?, ?)");
				$app->execute(array($appId, $cid, $car, date("Y-m-d H:i:s"), date("Y-m-d H:i:s")));

				$insert = $db->prepare("INSERT INTO tbl_app_type (appId, typeId) 
										VALUES(?, ?)");
				$insert->execute(array($appId, $type));
				
				$insert = $db->prepare("INSERT INTO tbl_app_service (appId, serviceId) 
										VALUES(?, ?)");
				$insert->execute(array($appId, $type));

				$transactions = $db->prepare("INSERT INTO tbl_transactions(tid, appId, cid, status, processedBy, dateProcessed)
											VALUES (?, ?, ?, ?, ?, ?)");
				$transactions->execute(array($tid, $appId, $cid, 1, $id, date("Y-m-d")." ".date("H:i:s")));
				$db->commit();
				echo "1";
			} catch(PDOException $e){
				echo $e;
				$db->rollBack();
			}
		}
	}
?>