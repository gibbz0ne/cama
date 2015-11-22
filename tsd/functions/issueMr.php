<?php
    include "../../class/connect.class.php";
    $con = new getConnection();
    $db = $con->PDO();
	$y = date("y");
	$userId = $_SESSION["userId"];

	$mrNo = "MR-M-".$y."-0001";
	if(isset($_POST["mrNum"]) && $_POST["mrNum"] != ""){
		if(strlen($_POST["mrNum"]) == 1)
			$mrNo = "MR-M-".$y."-000".$_POST["mrNum"];
		else if(strlen($_POST["mrNum"]) == 2)
			$mrNo = "MR-M-".$y."-00".$_POST["mrNum"];
		else if(strlen($_POST["mrNum"]) == 3)
			$mrNo = "MR-M-".$y."-0".$_POST["mrNum"];
		else
			$mrNo = "MR-M-".$y."-".$_POST["mrNum"];
	} else{
		$query = $db->query("SELECT *FROM tbl_mr WHERE mrNo LIKE '%$y%' ORDER BY mrNo DESC LIMIT 1");

		if($query->rowCount() > 0){
			foreach($query as $row){
				$mrNo = explode("-", $row["mrNo"]);
				$series = intval($mrNo[3]+1);

				if(strlen($series) == 1){
					$mrNo = "MR-M-".$y."-000".$series;
				} else if(strlen($series) == 2){
					$mrNo = "MR-M-".$y."-00".$series;
				} else if(strlen($series) == 3){
					$mrNo = "MR-M-".$y."-0".$series;
				} else{
					$mrNo = "MR-M-".$y."-".$series;
				}
			}
		}
	}

    if(isset($_POST["data3"])){
        $data = $_POST["data2"];
        $materials = $_POST["data3"];
        $purpose = $_POST["purpose"];
		$ctr = $ctr2 = $ctr3 = 1;
		try{
			$db->beginTransaction();
			$mr = $db->prepare("INSERT INTO tbl_mr (mrNo, mrDate, mrPurpose, isSend) VALUES (?, ?, ?, ?)");
			$mr->execute(array($mrNo, date("Y-m-d")." ".date("H:i:s"), strtoupper($purpose), 1));
			$mArray = array();
			for($i = 0; $i<count($materials); $i++){
				if($ctr2%5 == 0 && $i != 0){
					array_push($mArray, $materials[$i]);
					// echo $mArray[2]." - <br>";
					// print_r($mArray);
					foreach($db->query("SELECT *FROM tbl_materials WHERE materialDesc LIKE '".addslashes($mArray[2])."'") as $row);
						$entry_id = $row["entry_id"];
					
					$mrContent = $db->prepare("INSERT INTO tbl_mr_content (mrNo, entry_id, mrQuantity) VALUES (?, ?, ?)");
					$mrContent->execute(array($mrNo, $entry_id, $mArray[4]));
					$mArray = array();
				}
				else
					array_push($mArray, $materials[$i]);
				$ctr2++;
			}

			for($i = 0; $i < count($data); $i++){
				if($ctr%7 == 0 && $i != 0){
					array_push($mArray, $data[$i]);
					// print_r($mArray);
					foreach($db->query("SELECT *FROM tbl_temp_consumers JOIN tbl_applications USING (cid) WHERE AccountNumberT = '".$mArray[5]."'") as $row){
						$appId = $row["appId"];
						$cid = $row["cid"];

						$trans = $mArray[6];
						
						$processed = $t = 0;
						$ti = explode("-", $trans);
						$transaction = $db->query("SELECT * FROM tbl_transactions WHERE tid LIKE '%$ti[0]%'ORDER BY tid DESC LIMIT 1");
						if($transaction->rowCount() > 0){
							foreach($transaction as $row){
								$d = explode("-", $row["tid"]);
								$incr = $d[1]+1;
								$t = $d[0]."-".$incr;
								$processed = $row["processedBy"];
							}
						}

						$update = $db->prepare("UPDATE tbl_transactions SET action = ?, approvedBy = ?, dateApproved = ? WHERE tid = ?");
						$update->execute(array(1, $userId, date("Y-m-d H:i:s"), $trans));

						$insert = $db->prepare("INSERT INTO tbl_transactions (tid, appId, cid, status, processedBy, dateProcessed)
											VALUES(?, ?, ?, ?, ?, ?)");
						$insert->execute(array($t, $appId, $cid, 6, $processed, date("Y-m-d H:i:s")));
						
						$mr_wo = $db->prepare("INSERT INTO tbl_mr_wo (mrNo, appId, wo) VALUES	(?, ?, ?)");
						$mr_wo->execute(array($mrNo, $appId, $mArray[1]));
					}
					$mArray = array();
				}
				else
					array_push($mArray, $data[$i]);
				$ctr++;
			}
			$db->commit();
		} catch(PDOException $e){
			$db->rollBack();
			echo $e;
		}
    }
	echo $mrNo;
?>