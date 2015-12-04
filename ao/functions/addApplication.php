<?php
	include "../../class/connect.class.php";
	
	$id = $_SESSION["userId"];
	$con = new getConnection();
	$db = $con->PDO();
	$branch = $codeB = $_SESSION["branch"];
	$area = $codeA = $_SESSION["area"];
	$codeB[0] = "0";
	$codeA[0] = "0";
	$code = $codeB.$codeA."-".date("y-m");
	include "../../class/accountNum.class.php";
	$processNext = new getNextNum();
	$user = $_SESSION["username"];
	// $imgData = addslashes(file_get_contents($_FILES['uploader']['tmp_name']));
	// $imageProperties = getimageSize($_FILES['uploader']['tmp_name']);

	if(isset($_POST["fname"])){
		$acctNum = null;
		$primary = (isset($_POST["primary"]) ? $_POST["primary"] : "");
		$email = $_POST["email"];
		$phone = $_POST["phone"];
		// $count = ($_POST["count"] != "" ? $_POST["count"] : 1);
		$remarks = (isset($_POST["remarks"]) ? str_replace("ñ", "Ñ", strtoupper($_POST["remarks"])) : NULL);
		$fname = str_replace("ñ", "Ñ", strtoupper($_POST["fname"]));
		$mname = str_replace("ñ", "Ñ", strtoupper($_POST["mname"]));
		$lname = str_replace("ñ", "Ñ", strtoupper($_POST["lname"]));
		$ename = strtoupper($_POST["ename"]);
		$civilStatus =strtoupper( $_POST["civilStatus"]);
		$spouseName = str_replace("ñ", "Ñ", strtoupper($_POST["spouseName"]));
		$hno = strtoupper($_POST["hno"]);
		$purok = strtoupper($_POST["purok"]);
		$brgy = strtoupper($_POST["brgy"]);
		$municipality = $_POST["municipality"];
		$customerType = $_POST["customerType"];
		$isBapa = ($_POST["isBapa"] == "BAPA" ? 1 : 0);
		$book = $_POST["bookNo"];
		$appId = date("Ymd")."001";
		$cid = $user."-".date("y")[1]."0001";
		$tid = $user."-".date("y")."00001";
		$brgy = $_POST["brgy"];
		$middle = $mname;
		$mname = ($mname == "" ? "" : " ".$mname[0].".");
		$hno = ($hno == "" ? "" : $hno);
		$purok = ($purok == "" ? "" : $purok);
		$serviceType = $car = 1;
		$tempDate = "0000-00-00";
		if(isset($_POST["isTemp"])){
			$tempDate = $_POST["tempDate"];
			$serviceType = 4;
		}
		
		$query = $db->query("SELECT *FROM tbl_applications WHERE appCAR LIKE '%$code%' ORDER BY appCAR DESC LIMIT 1");
		if($query->rowCount() > 0){
			foreach($query as $row){
				$e = explode("-", $row["appCAR"]);
				$incr = intval($e[2]+1);
				$car = $e[0]."-".$e[1]."-".$incr;
			}
		} else
			$car = $code."0001";

		$query = $db->query("SELECT *FROM tbl_municipality WHERE munId = '$municipality'");
		foreach($query as $row)
			$municipality = $row["munDesc"];
			
		$query = $db->query("SELECT *FROM tbl_barangay WHERE brgyId = '$brgy'");
		foreach($query as $row)
			$brgy = $row["brgyName"];
		
		$consumer = $db->query("SELECT * FROM tbl_temp_consumers WHERE cid LIKE '%$user-".date("y")[1]."%'ORDER BY cid DESC LIMIT 1");
		if($consumer->rowCount() > 0){
			foreach($consumer as $row){
				$d = explode("-", $row["cid"]);
				$incr = intval($d[1]+1);
				$cid = $user."-".$incr;
			}
		}
		
		$query = $db->query("SELECT *FROM tbl_applications ORDER BY appId DESC LIMIT 1");
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
		
		
		
		$transaction = $db->query("SELECT * FROM tbl_transactions WHERE tid LIKE '%$user-".date("y")."%'ORDER BY tid DESC LIMIT 1");
		if($transaction->rowCount() > 0){
			foreach($transaction as $row){
				$d = explode("-", $row["tid"]);
				$incr = $d[1]+1;
				$tid = $user."-".$incr;
			}
		}
		
		try{
			$db->beginTransaction();
			
			$insertC = $db->prepare("INSERT INTO tbl_temp_consumers (cid, AccountNameT, MiddleName, AddressT, BarangayT, BranchT, MunicipalityT, CustomerTypeT, bapaT, bookT)
									 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$insertC->execute(array($cid, $lname." ".$fname.$mname." ".$ename, $middle, $hno." ".$purok." ".$brgy." ".$municipality, $brgy, $branch, $municipality, $customerType, $isBapa, $book));
			
			$app = $db->prepare("INSERT INTO tbl_applications (appId, cid, appDate, appCAR, dateCAR)
								 VALUES (?, ?, ?, ?, ?)");
			$app->execute(array($appId, $cid, date("Y-m-d H:i:s"), $car, date("Y-m-d H:i:s")));
				
			$transactions = $db->prepare("INSERT INTO tbl_transactions(tid, appId, cid, status, processedBy, dateProcessed, remarks)
										VALUES (?, ?, ?, ?, ?, ?, ?)");
			$transactions->execute(array($tid, $appId, $cid, $serviceType, $id, date("Y-m-d")." ".date("H:i:s"), $remarks));
			
			$insertR = $db->prepare("INSERT INTO tbl_consumer_relation (cid, relationName, relationStatus) VALUES (?, ?, ?)");
			$insertR->execute(array($cid, $spouseName, $civilStatus));

			$insert = $db->prepare("INSERT INTO tbl_app_type (appId, typeId) 
											 VALUES(?, ?)");
			$insert->execute(array($appId, 1));
			
			$insert = $db->prepare("INSERT INTO tbl_app_service (appId, serviceId) 
											 VALUES(?, ?)");
			$insert->execute(array($appId, 1));
			
			$contact = $db->prepare("INSERT INTO tbl_consumer_contact(cid, contactNo, contactEmail) VALUES (?, ?, ?)");
			$contact->execute(array($cid, $phone, $email));
		
			$db->commit();
			echo true;
		}catch(PDOException $e){
			$db->rollBack();
			echo $e;
		}
	}
?>